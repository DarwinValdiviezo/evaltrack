#!/bin/bash

# Script de Despliegue Automatizado para EvalTrack
# Versión: 1.0.0
# Uso: ./scripts/deploy.sh [environment] [version]

set -e

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Variables por defecto
ENVIRONMENT=${1:-production}
VERSION=${2:-latest}
DOCKER_REGISTRY="company"
IMAGE_NAME="evaltrack"
FULL_IMAGE_NAME="$DOCKER_REGISTRY/$IMAGE_NAME:$VERSION"

# Función para logging
log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

success() {
    echo -e "${GREEN}✅ $1${NC}"
}

warning() {
    echo -e "${YELLOW}⚠️ $1${NC}"
}

error() {
    echo -e "${RED}❌ $1${NC}"
}

# Función para verificar prerequisitos
check_prerequisites() {
    log "Verificando prerequisitos..."
    
    # Verificar Docker
    if ! command -v docker &> /dev/null; then
        error "Docker no está instalado"
        exit 1
    fi
    
    # Verificar kubectl
    if ! command -v kubectl &> /dev/null; then
        error "kubectl no está instalado"
        exit 1
    fi
    
    # Verificar conexión al cluster
    if ! kubectl cluster-info &> /dev/null; then
        error "No se puede conectar al cluster de Kubernetes"
        exit 1
    fi
    
    success "Prerequisitos verificados"
}

# Función para backup de base de datos
backup_database() {
    log "Creando backup de base de datos..."
    
    TIMESTAMP=$(date +%Y%m%d_%H%M%S)
    BACKUP_DIR="/backups/evaltrack_${ENVIRONMENT}_${TIMESTAMP}"
    
    mkdir -p "$BACKUP_DIR"
    
    # Backup PostgreSQL
    if kubectl get pods -n evaltrack-$ENVIRONMENT -l app=postgres &> /dev/null; then
        log "Backup de PostgreSQL..."
        kubectl exec -n evaltrack-$ENVIRONMENT deployment/postgres -- \
            pg_dump -U evaltrack_user evaltrack_users > "$BACKUP_DIR/postgres_backup.sql"
        success "Backup de PostgreSQL completado"
    fi
    
    # Backup MySQL
    if kubectl get pods -n evaltrack-$ENVIRONMENT -l app=mysql &> /dev/null; then
        log "Backup de MySQL..."
        kubectl exec -n evaltrack-$ENVIRONMENT deployment/mysql -- \
            mysqldump -u evaltrack_user -p$MYSQL_PASSWORD evaltrack_business > "$BACKUP_DIR/mysql_backup.sql"
        success "Backup de MySQL completado"
    fi
    
    log "Backup guardado en: $BACKUP_DIR"
}

# Función para verificar health check
health_check() {
    local url=$1
    local max_attempts=30
    local attempt=1
    
    log "Verificando health check en $url..."
    
    while [ $attempt -le $max_attempts ]; do
        if curl -f -s "$url/health" > /dev/null; then
            success "Health check exitoso"
            return 0
        fi
        
        log "Intento $attempt/$max_attempts - Health check falló..."
        sleep 10
        attempt=$((attempt + 1))
    done
    
    error "Health check falló después de $max_attempts intentos"
    return 1
}

# Función para rollback
rollback() {
    error "Ejecutando rollback..."
    
    case $ENVIRONMENT in
        "production")
            # Rollback Blue/Green
            kubectl patch service evaltrack-web-service -n evaltrack-prod \
                -p '{"spec":{"selector":{"environment":"blue"}}}'
            kubectl scale deployment evaltrack-web-green -n evaltrack-prod --replicas=0
            success "Rollback completado - Tráfico redirigido a Blue"
            ;;
        *)
            # Rollback normal
            kubectl rollout undo deployment/evaltrack-web -n evaltrack-$ENVIRONMENT
            kubectl rollout status deployment/evaltrack-web -n evaltrack-$ENVIRONMENT --timeout=300s
            success "Rollback completado"
            ;;
    esac
}

# Función para despliegue Blue/Green
blue_green_deploy() {
    log "Iniciando despliegue Blue/Green..."
    
    # Escalar Green a 3 réplicas
    kubectl scale deployment evaltrack-web-green -n evaltrack-prod --replicas=3
    
    # Esperar que Green esté listo
    kubectl rollout status deployment/evaltrack-web-green -n evaltrack-prod --timeout=300s
    
    # Health check en Green
    if ! health_check "http://evaltrack-green.company.com"; then
        error "Health check falló en Green environment"
        kubectl scale deployment evaltrack-web-green -n evaltrack-prod --replicas=0
        exit 1
    fi
    
    # Cambiar tráfico a Green
    kubectl patch service evaltrack-web-service -n evaltrack-prod \
        -p '{"spec":{"selector":{"environment":"green"}}}'
    
    # Esperar que el tráfico se estabilice
    sleep 30
    
    # Health check en producción
    if ! health_check "https://evaltrack.company.com"; then
        error "Health check falló en producción después del switch"
        rollback
        exit 1
    fi
    
    # Escalar Blue a 0 réplicas
    kubectl scale deployment evaltrack-web-blue -n evaltrack-prod --replicas=0
    
    success "Despliegue Blue/Green completado exitosamente"
}

# Función para despliegue normal
normal_deploy() {
    log "Iniciando despliegue normal..."
    
    # Actualizar imagen
    kubectl set image deployment/evaltrack-web evaltrack=$FULL_IMAGE_NAME -n evaltrack-$ENVIRONMENT
    
    # Esperar rollout
    kubectl rollout status deployment/evaltrack-web -n evaltrack-$ENVIRONMENT --timeout=300s
    
    # Health check
    local url
    case $ENVIRONMENT in
        "development")
            url="http://evaltrack-dev.company.com"
            ;;
        "staging")
            url="http://evaltrack-staging.company.com"
            ;;
        *)
            url="https://evaltrack.company.com"
            ;;
    esac
    
    if ! health_check "$url"; then
        error "Health check falló"
        rollback
        exit 1
    fi
    
    success "Despliegue normal completado exitosamente"
}

# Función para notificar
notify() {
    local status=$1
    local message=$2
    
    # Notificar a Slack
    if [ -n "$SLACK_WEBHOOK" ]; then
        curl -X POST -H 'Content-type: application/json' \
            --data "{\"text\":\"🚀 EvalTrack v$VERSION - $status: $message\"}" \
            "$SLACK_WEBHOOK"
    fi
    
    # Notificar por email
    if [ -n "$EMAIL_RECIPIENTS" ]; then
        echo "EvalTrack v$VERSION - $status: $message" | \
            mail -s "Despliegue EvalTrack $ENVIRONMENT" $EMAIL_RECIPIENTS
    fi
}

# Función principal
main() {
    echo "=========================================="
    echo "   EvalTrack v$VERSION - Despliegue"
    echo "   Entorno: $ENVIRONMENT"
    echo "=========================================="
    
    # Verificar prerequisitos
    check_prerequisites
    
    # Verificar que la imagen existe
    log "Verificando imagen Docker: $FULL_IMAGE_NAME"
    if ! docker pull $FULL_IMAGE_NAME &> /dev/null; then
        error "No se puede descargar la imagen $FULL_IMAGE_NAME"
        exit 1
    fi
    success "Imagen verificada"
    
    # Backup de base de datos
    backup_database
    
    # Despliegue según el entorno
    case $ENVIRONMENT in
        "production")
            blue_green_deploy
            ;;
        "development"|"staging")
            normal_deploy
            ;;
        *)
            error "Entorno no válido: $ENVIRONMENT"
            exit 1
            ;;
    esac
    
    # Notificar éxito
    notify "ÉXITO" "Despliegue completado en $ENVIRONMENT"
    
    echo "=========================================="
    success "Despliegue completado exitosamente!"
    echo "   Entorno: $ENVIRONMENT"
    echo "   Versión: $VERSION"
    echo "   Imagen: $FULL_IMAGE_NAME"
    echo "=========================================="
}

# Manejo de errores
trap 'error "Error en línea $LINENO. Ejecutando rollback..."; rollback; exit 1' ERR

# Ejecutar función principal
main "$@" 