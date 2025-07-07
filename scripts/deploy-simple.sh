#!/bin/bash

# Script de despliegue simplificado para EvalTrack
# Uso: ./scripts/deploy-simple.sh [environment]
# Entornos: local, staging, production

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
LOG_FILE="storage/logs/deploy-$(date +%Y%m%d_%H%M%S).log"
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

# Crear directorio de logs si no existe
mkdir -p storage/logs
mkdir -p $BACKUP_DIR

log "🚀 Iniciando despliegue simplificado de EvalTrack en entorno: $ENVIRONMENT"

# Función para backup automático
backup_automatic() {
    log "📦 Creando backup automático..."
    
    TIMESTAMP=$(date +%Y%m%d_%H%M%S)
    
    # Backup de archivos críticos
    tar -czf "$BACKUP_DIR/files_$TIMESTAMP.tar.gz" \
        --exclude='vendor' \
        --exclude='node_modules' \
        --exclude='storage/logs' \
        --exclude='.git' \
        .
    
    # Backup de base de datos PostgreSQL
    if command -v pg_dump &> /dev/null; then
        pg_dump -h localhost -U evaltrack_user evaltrack_users > "$BACKUP_DIR/postgres_$TIMESTAMP.sql" 2>/dev/null || warning "No se pudo hacer backup de PostgreSQL"
    fi
    
    # Backup de base de datos MySQL
    if command -v mysqldump &> /dev/null; then
        mysqldump -h localhost -u evaltrack_user -p evaltrack_business > "$BACKUP_DIR/mysql_$TIMESTAMP.sql" 2>/dev/null || warning "No se pudo hacer backup de MySQL"
    fi
    
    # Guardar timestamp del backup
    echo $TIMESTAMP > "$BACKUP_DIR/last_backup.txt"
    
    success "Backup completado: $TIMESTAMP"
}

# Función para actualizar código
update_code() {
    log "📥 Actualizando código desde repositorio..."
    
    # Verificar si hay cambios pendientes
    if [ -n "$(git status --porcelain)" ]; then
        warning "Hay cambios sin commit. Guardando stash..."
        git stash push -m "Auto-stash before deployment $(date)"
    fi
    
    # Pull del código más reciente
    git pull origin main
    
    success "Código actualizado"
}

# Función para instalar dependencias
install_dependencies() {
    log "📦 Instalando dependencias..."
    
    # Instalar dependencias PHP
    if [ -f "composer.json" ]; then
        composer install --no-dev --optimize-autoloader --no-interaction
        success "Dependencias PHP instaladas"
    fi
    
    # Instalar dependencias Node.js
    if [ -f "package.json" ]; then
        npm ci --production --silent
        success "Dependencias Node.js instaladas"
    fi
}

# Función para configurar entorno
setup_environment() {
    log "⚙️ Configurando entorno..."
    
    # Copiar archivo de entorno si no existe
    if [ ! -f ".env" ]; then
        cp .env.example .env
        warning "Archivo .env creado desde .env.example. Revisa la configuración."
    fi
    
    # Generar clave de aplicación
    php artisan key:generate --force
    
    # Limpiar caché
    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear
    php artisan route:clear
    
    success "Entorno configurado"
}

# Función para migrar base de datos
migrate_database() {
    log "🗄️ Ejecutando migraciones..."
    
    # Migrar base de datos de usuarios (PostgreSQL)
    php artisan migrate --database=pgsql --path=database/migrations/users --force || error "Error en migración PostgreSQL"
    
    # Migrar base de datos de negocio (MySQL)
    php artisan migrate --database=mysql_business --path=database/migrations/business --force || error "Error en migración MySQL"
    
    success "Migraciones completadas"
}

# Función para ejecutar seeders (solo en desarrollo)
run_seeders() {
    if [ "$ENVIRONMENT" = "local" ] || [ "$ENVIRONMENT" = "development" ]; then
        log "🌱 Ejecutando seeders..."
        php artisan db:seed --force
        success "Seeders ejecutados"
    else
        log "⏭️ Saltando seeders en entorno $ENVIRONMENT"
    fi
}

# Función para optimizar aplicación
optimize_application() {
    log "⚡ Optimizando aplicación..."
    
    # Compilar assets
    npm run build --silent
    
    # Optimizar configuración
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    # Optimizar autoloader
    composer dump-autoload --optimize --no-dev
    
    success "Aplicación optimizada"
}

# Función para verificar permisos
check_permissions() {
    log "🔐 Verificando permisos..."
    
    # Crear directorios necesarios
    mkdir -p bootstrap/cache
    mkdir -p storage/framework/{cache,sessions,views}
    mkdir -p storage/logs
    
    # Establecer permisos
    chmod -R 775 storage
    chmod -R 775 bootstrap/cache
    
    success "Permisos configurados"
}

# Función para health check
health_check() {
    log "🏥 Ejecutando health check..."
    
    # Esperar un momento para que la aplicación se estabilice
    sleep 5
    
    # Verificar que la aplicación responde
    if curl -f http://localhost/health > /dev/null 2>&1; then
        success "Health check exitoso"
    else
        warning "Health check falló - verifica que el servidor esté ejecutándose"
        return 1
    fi
}

# Función para notificar resultado
notify_result() {
    local status=$1
    local message=$2
    
    log "📧 Notificando resultado: $status"
    
    # Aquí puedes agregar notificaciones por email, Slack, etc.
    # Por ahora solo lo guardamos en el log
    
    if [ "$status" = "SUCCESS" ]; then
        success "Despliegue completado exitosamente"
    else
        error "Despliegue falló: $message"
    fi
}

# Función para rollback automático
rollback_automatic() {
    log "🔄 Iniciando rollback automático..."
    
    # Restaurar código
    git reset --hard HEAD~1
    
    # Limpiar caché
    php artisan cache:clear
    php artisan config:clear
    
    # Restaurar último backup si existe
    if [ -f "$BACKUP_DIR/last_backup.txt" ]; then
        LAST_BACKUP=$(cat "$BACKUP_DIR/last_backup.txt")
        log "Restaurando backup: $LAST_BACKUP"
        # Aquí irían los comandos de restauración
    fi
    
    error "Rollback completado - verifica la aplicación"
}

# Función principal de despliegue
main() {
    local start_time=$(date +%s)
    
    case $ENVIRONMENT in
        "local"|"development"|"staging"|"production")
            log "Iniciando despliegue en entorno: $ENVIRONMENT"
            
            # Ejecutar pasos de despliegue
            backup_automatic
            update_code
            install_dependencies
            setup_environment
            check_permissions
            migrate_database
            run_seeders
            optimize_application
            
            # Health check con rollback automático si falla
            if ! health_check; then
                rollback_automatic
            fi
            
            local end_time=$(date +%s)
            local duration=$((end_time - start_time))
            
            success "🎉 Despliegue completado en ${duration} segundos!"
            log "🌐 La aplicación debería estar disponible en: http://localhost"
            
            notify_result "SUCCESS" "Despliegue completado en ${duration}s"
            ;;
        *)
            error "Entorno no válido. Usa: local, development, staging, o production"
            ;;
    esac
}

# Capturar errores y hacer rollback
trap 'if [ $? -ne 0 ]; then log "Error detectado, iniciando rollback..."; rollback_automatic; fi' EXIT

# Ejecutar función principal
main "$@" 