#!/bin/bash

# ========================================
# SCRIPT DE DESPLIEGUE AUTOMATIZADO
# ========================================

set -e  # Salir en caso de error

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Función para logging
log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

success() {
    echo -e "${GREEN}✅ $1${NC}"
}

warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

error() {
    echo -e "${RED}❌ $1${NC}"
}

# Variables de configuración
VERSION=${1:-latest}
ENVIRONMENT=${2:-production}
BACKUP_DIR="./backups"
LOG_DIR="./logs"

# Crear directorios si no existen
mkdir -p $BACKUP_DIR $LOG_DIR

# Función para backup de base de datos
backup_database() {
    log "Iniciando backup de base de datos..."
    
    if [ -z "$DATABASE_URL" ]; then
        warning "DATABASE_URL no configurada, saltando backup"
        return 0
    fi
    
    BACKUP_FILE="$BACKUP_DIR/backup_$(date +%Y%m%d_%H%M%S).sql"
    
    if pg_dump "$DATABASE_URL" > "$BACKUP_FILE" 2>/dev/null; then
        success "Backup creado: $BACKUP_FILE"
    else
        warning "No se pudo crear backup de base de datos"
    fi
}

# Función para health check
health_check() {
    log "Realizando health check..."
    
    local max_attempts=10
    local attempt=1
    
    while [ $attempt -le $max_attempts ]; do
        if curl -f http://localhost:3000/health >/dev/null 2>&1; then
            success "Health check exitoso"
            return 0
        fi
        
        log "Intento $attempt/$max_attempts - Esperando 10 segundos..."
        sleep 10
        ((attempt++))
    done
    
    error "Health check falló después de $max_attempts intentos"
    return 1
}

# Función para rollback
rollback() {
    error "Iniciando rollback..."
    
    # Detener contenedor actual
    docker stop nestjs-api-prod 2>/dev/null || true
    docker rm nestjs-api-prod 2>/dev/null || true
    
    # Restaurar versión anterior
    if docker images | grep -q "nestjs-api:previous"; then
        docker run -d \
            --name nestjs-api-prod \
            --network app-network \
            -p 3000:3000 \
            --env-file prod.env \
            --restart unless-stopped \
            nestjs-api:previous
        
        success "Rollback completado - versión anterior restaurada"
    else
        error "No se encontró versión anterior para rollback"
        exit 1
    fi
}

# Función para notificar
notify() {
    local message="$1"
    local level="${2:-info}"
    
    # Aquí puedes agregar notificaciones a Slack, email, etc.
    log "NOTIFICACIÓN [$level]: $message"
    
    # Ejemplo para Slack (descomenta y configura)
    # if [ -n "$SLACK_WEBHOOK_URL" ]; then
    #     curl -X POST -H 'Content-type: application/json' \
    #         --data "{\"text\":\"[$level] $message\"}" \
    #         "$SLACK_WEBHOOK_URL" >/dev/null 2>&1
    # fi
}

# Función principal de despliegue
deploy() {
    log "🚀 Iniciando despliegue v$VERSION a $ENVIRONMENT"
    
    # 1. Validaciones pre-despliegue
    log "📋 Validaciones pre-despliegue..."
    
    if [ ! -f "prod.env" ]; then
        error "Archivo prod.env no encontrado"
        exit 1
    fi
    
    if [ ! -f "Dockerfile" ]; then
        error "Dockerfile no encontrado"
        exit 1
    fi
    
    success "Validaciones completadas"
    
    # 2. Backup de base de datos
    backup_database
    
    # 3. Etiquetar versión actual como previous
    if docker images | grep -q "nestjs-api:latest"; then
        docker tag nestjs-api:latest nestjs-api:previous
        log "Versión actual etiquetada como previous"
    fi
    
    # 4. Construir nueva imagen
    log "🔨 Construyendo imagen Docker..."
    docker build -t nestjs-api:$VERSION .
    docker tag nestjs-api:$VERSION nestjs-api:latest
    success "Imagen construida exitosamente"
    
    # 5. Detener contenedor actual
    log "🛑 Deteniendo contenedor actual..."
    docker stop nestjs-api-prod 2>/dev/null || true
    docker rm nestjs-api-prod 2>/dev/null || true
    
    # 6. Desplegar nueva versión
    log "🚀 Desplegando nueva versión..."
    docker run -d \
        --name nestjs-api-prod \
        --network app-network \
        -p 3000:3000 \
        --env-file prod.env \
        --restart unless-stopped \
        nestjs-api:$VERSION
    
    success "Contenedor desplegado"
    
    # 7. Health check
    if health_check; then
        success "Despliegue completado exitosamente"
        notify "Despliegue v$VERSION completado exitosamente" "success"
    else
        error "Health check falló"
        notify "Despliegue v$VERSION falló - iniciando rollback" "error"
        rollback
        exit 1
    fi
    
    # 8. Limpieza de imágenes antiguas
    log "🧹 Limpiando imágenes antiguas..."
    docker image prune -f
    success "Limpieza completada"
}

# Función para mostrar ayuda
show_help() {
    echo "Uso: $0 [VERSION] [ENVIRONMENT]"
    echo ""
    echo "Argumentos:"
    echo "  VERSION     Versión a desplegar (default: latest)"
    echo "  ENVIRONMENT Entorno de despliegue (default: production)"
    echo ""
    echo "Ejemplos:"
    echo "  $0                    # Desplegar latest en production"
    echo "  $0 v1.0.0            # Desplegar v1.0.0 en production"
    echo "  $0 v1.0.0 staging    # Desplegar v1.0.0 en staging"
    echo ""
    echo "Comandos adicionales:"
    echo "  $0 rollback          # Hacer rollback a versión anterior"
    echo "  $0 status            # Verificar estado del despliegue"
}

# Función para verificar estado
check_status() {
    log "📊 Verificando estado del despliegue..."
    
    echo ""
    echo "=== Estado de Contenedores ==="
    docker ps --filter "name=nestjs-api" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
    
    echo ""
    echo "=== Health Check ==="
    if curl -f http://localhost:3000/health >/dev/null 2>&1; then
        success "API respondiendo correctamente"
    else
        error "API no responde"
    fi
    
    echo ""
    echo "=== Logs Recientes ==="
    docker logs --tail 10 nestjs-api-prod 2>/dev/null || echo "Contenedor no encontrado"
}

# Manejo de argumentos
case "${1:-}" in
    "help"|"-h"|"--help")
        show_help
        exit 0
        ;;
    "rollback")
        rollback
        exit 0
        ;;
    "status")
        check_status
        exit 0
        ;;
    *)
        deploy
        ;;
esac 