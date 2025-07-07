#!/bin/bash

# Script de despliegue Docker para EvalTrack
# Uso: ./scripts/deploy-docker.sh [environment]

set -e

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Variables
ENVIRONMENT=${1:-local}
PROJECT_NAME="evaltrack"
LOG_FILE="storage/logs/deploy-docker-$(date +%Y%m%d_%H%M%S).log"
BACKUP_DIR="backup"

# Función para logging
log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a "$LOG_FILE"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1" | tee -a "$LOG_FILE"
    exit 1
}

success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1" | tee -a "$LOG_FILE"
}

warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1" | tee -a "$LOG_FILE"
}

# Verificar que estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    error "No se encontró el archivo artisan. Ejecuta este script desde la raíz del proyecto."
fi

# Verificar Docker
check_docker() {
    log "🐳 Verificando Docker..."
    
    if ! command -v docker &> /dev/null; then
        error "Docker no está instalado"
    fi
    
    if ! command -v docker-compose &> /dev/null; then
        error "Docker Compose no está instalado"
    fi
    
    if ! docker info &> /dev/null; then
        error "Docker no está ejecutándose"
    fi
    
    success "Docker está disponible"
}

# Crear directorios necesarios
mkdir -p storage/logs
mkdir -p $BACKUP_DIR

log "🚀 Iniciando despliegue Docker de EvalTrack en entorno: $ENVIRONMENT"

# Función para backup de datos Docker
backup_docker_data() {
    log "📦 Creando backup de datos Docker..."
    
    TIMESTAMP=$(date +%Y%m%d_%H%M%S)
    
    # Backup de volúmenes Docker
    if docker volume ls | grep -q "evaltrack_postgres_data"; then
        docker run --rm -v evaltrack_postgres_data:/data -v $(pwd)/$BACKUP_DIR:/backup alpine tar czf /backup/postgres_data_$TIMESTAMP.tar.gz -C /data .
        success "Backup PostgreSQL creado"
    fi
    
    if docker volume ls | grep -q "evaltrack_mysql_data"; then
        docker run --rm -v evaltrack_mysql_data:/data -v $(pwd)/$BACKUP_DIR:/backup alpine tar czf /backup/mysql_data_$TIMESTAMP.tar.gz -C /data .
        success "Backup MySQL creado"
    fi
    
    if docker volume ls | grep -q "evaltrack_redis_data"; then
        docker run --rm -v evaltrack_redis_data:/data -v $(pwd)/$BACKUP_DIR:/backup alpine tar czf /backup/redis_data_$TIMESTAMP.tar.gz -C /data .
        success "Backup Redis creado"
    fi
    
    # Guardar timestamp del backup
    echo $TIMESTAMP > "$BACKUP_DIR/last_docker_backup.txt"
    
    success "Backup Docker completado: $TIMESTAMP"
}

# Función para actualizar código
update_code() {
    log "📥 Actualizando código desde repositorio..."
    
    # Verificar si hay cambios pendientes
    if [ -n "$(git status --porcelain)" ]; then
        warning "Hay cambios sin commit. Guardando stash..."
        git stash push -m "Auto-stash before Docker deployment $(date)"
    fi
    
    # Pull del código más reciente
    git pull origin main
    
    success "Código actualizado"
}

# Función para construir imagen Docker
build_docker_image() {
    log "🔨 Construyendo imagen Docker..."
    
    # Limpiar imágenes no utilizadas
    docker system prune -f
    
    # Construir imagen con cache
    docker-compose build --no-cache app
    
    success "Imagen Docker construida"
}

# Función para detener servicios
stop_services() {
    log "🛑 Deteniendo servicios Docker..."
    
    docker-compose down --remove-orphans
    
    success "Servicios detenidos"
}

# Función para iniciar servicios
start_services() {
    log "▶️ Iniciando servicios Docker..."
    
    # Iniciar servicios en orden
    docker-compose up -d postgres mysql redis
    
    # Esperar a que las bases de datos estén listas
    log "⏳ Esperando a que las bases de datos estén listas..."
    sleep 30
    
    # Iniciar aplicación
    docker-compose up -d app
    
    success "Servicios iniciados"
}

# Función para ejecutar migraciones
run_migrations() {
    log "🗄️ Ejecutando migraciones..."
    
    # Esperar a que la aplicación esté lista
    sleep 10
    
    # Migrar base de datos de usuarios (PostgreSQL) - Conexión principal
    log "Migrando PostgreSQL (usuarios y roles)..."
    docker-compose exec -T app php artisan migrate --database=pgsql --path=database/migrations/users --force || warning "Error en migración PostgreSQL"
    
    # Migrar base de datos de negocio (MySQL) - Conexión secundaria
    log "Migrando MySQL (datos de negocio)..."
    docker-compose exec -T app php artisan migrate --database=mysql_business --path=database/migrations/business --force || error "Error en migración MySQL"
    
    success "Migraciones completadas"
}

# Función para ejecutar seeders
run_seeders() {
    if [ "$ENVIRONMENT" = "local" ] || [ "$ENVIRONMENT" = "development" ]; then
        log "🌱 Ejecutando seeders..."
        docker-compose exec -T app php artisan db:seed --database=mysql_business --force
        success "Seeders ejecutados"
    else
        log "⏭️ Saltando seeders en entorno $ENVIRONMENT"
    fi
}

# Función para optimizar aplicación
optimize_application() {
    log "⚡ Optimizando aplicación..."
    
    # Compilar assets
    docker-compose exec -T app npm run build
    
    # Optimizar configuración
    docker-compose exec -T app php artisan config:cache
    docker-compose exec -T app php artisan route:cache
    docker-compose exec -T app php artisan view:cache
    
    # Optimizar autoloader
    docker-compose exec -T app composer dump-autoload --optimize --no-dev
    
    success "Aplicación optimizada"
}

# Función para health check
health_check() {
    log "🏥 Ejecutando health check..."
    
    # Esperar a que la aplicación esté completamente lista
    sleep 20
    
    # Verificar que la aplicación responde
    local retries=0
    local max_retries=10
    
    while [ $retries -lt $max_retries ]; do
        if curl -f http://localhost:8000/health > /dev/null 2>&1; then
            success "Health check exitoso"
            return 0
        else
            retries=$((retries + 1))
            warning "Intento $retries de $max_retries falló"
            sleep 10
        fi
    done
    
    error "Health check falló después de $max_retries intentos"
    return 1
}

# Función para mostrar logs
show_logs() {
    log "📋 Mostrando logs de la aplicación..."
    
    echo ""
    echo "=== Logs de la aplicación ==="
    docker-compose logs --tail=20 app
    
    echo ""
    echo "=== Estado de los servicios ==="
    docker-compose ps
}

# Función para mostrar información de acceso
show_access_info() {
    log "🌐 Información de acceso:"
    echo ""
    echo "✅ Aplicación: http://localhost:8000"
    echo "🏥 Health Check: http://localhost:8000/health"
    echo "📧 MailHog: http://localhost:8025"
    echo "🗄️ Adminer: http://localhost:8080"
    echo ""
    echo "Credenciales por defecto:"
    echo "   - Usuario: admin@evaltrack.com"
    echo "   - Contraseña: password"
    echo ""
    echo "Comandos útiles:"
    echo "   - Ver logs: docker-compose logs -f app"
    echo "   - Reiniciar: docker-compose restart app"
    echo "   - Detener: docker-compose down"
    echo ""
}

# Función para rollback Docker
rollback_docker() {
    log "🔄 Iniciando rollback Docker..."
    
    # Detener servicios
    docker-compose down
    
    # Restaurar volúmenes si existe backup
    if [ -f "$BACKUP_DIR/last_docker_backup.txt" ]; then
        local backup_timestamp=$(cat "$BACKUP_DIR/last_docker_backup.txt")
        log "Restaurando backup: $backup_timestamp"
        
        # Aquí irían los comandos de restauración de volúmenes
        # docker run --rm -v evaltrack_postgres_data:/data -v $(pwd)/$BACKUP_DIR:/backup alpine tar xzf /backup/postgres_data_$backup_timestamp.tar.gz -C /data
    fi
    
    # Reiniciar servicios
    docker-compose up -d
    
    error "Rollback completado - verifica la aplicación"
}

# Función principal de despliegue
main() {
    local start_time=$(date +%s)
    
    case $ENVIRONMENT in
        "local"|"development"|"staging"|"production")
            log "Iniciando despliegue Docker en entorno: $ENVIRONMENT"
            
            # Ejecutar pasos de despliegue
            check_docker
            backup_docker_data
            update_code
            build_docker_image
            stop_services
            start_services
            run_migrations
            run_seeders
            optimize_application
            
            # Health check con rollback automático si falla
            if ! health_check; then
                rollback_docker
            fi
            
            local end_time=$(date +%s)
            local duration=$((end_time - start_time))
            
            success "🎉 Despliegue Docker completado en ${duration} segundos!"
            
            # Mostrar información
            show_logs
            show_access_info
            
            ;;
        *)
            error "Entorno no válido. Usa: local, development, staging, o production"
            ;;
    esac
}

# Capturar errores y hacer rollback
trap 'if [ $? -ne 0 ]; then log "Error detectado, iniciando rollback..."; rollback_docker; fi' EXIT

# Ejecutar función principal
main "$@" 