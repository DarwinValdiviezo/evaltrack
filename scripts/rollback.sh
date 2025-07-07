#!/bin/bash

# Script de rollback para EvalTrack
# Uso: ./scripts/rollback.sh [backup_timestamp]
# Si no se especifica timestamp, usa el último backup

set -e

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Variables
BACKUP_DIR="backup"
TIMESTAMP=${1:-$(cat "$BACKUP_DIR/last_backup.txt" 2>/dev/null || echo "")}
LOG_FILE="storage/logs/rollback-$(date +%Y%m%d_%H%M%S).log"

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

log "🔄 Iniciando rollback de EvalTrack..."

# Función para verificar si existe el backup
check_backup_exists() {
    if [ -z "$TIMESTAMP" ]; then
        error "No se especificó timestamp de backup y no se encontró backup reciente"
    fi
    
    if [ ! -f "$BACKUP_DIR/files_$TIMESTAMP.tar.gz" ]; then
        error "No se encontró backup de archivos: files_$TIMESTAMP.tar.gz"
    fi
    
    log "📦 Backup encontrado: $TIMESTAMP"
}

# Función para hacer backup del estado actual antes del rollback
backup_current_state() {
    log "📦 Creando backup del estado actual antes del rollback..."
    
    local current_timestamp=$(date +%Y%m%d_%H%M%S)
    
    # Backup de archivos actuales
    tar -czf "$BACKUP_DIR/pre_rollback_$current_timestamp.tar.gz" \
        --exclude='vendor' \
        --exclude='node_modules' \
        --exclude='storage/logs' \
        --exclude='.git' \
        .
    
    success "Backup del estado actual creado: pre_rollback_$current_timestamp"
}

# Función para restaurar código
restore_code() {
    log "📥 Restaurando código desde backup..."
    
    # Verificar si hay cambios sin commit
    if [ -n "$(git status --porcelain)" ]; then
        warning "Hay cambios sin commit. Guardando stash..."
        git stash push -m "Auto-stash before rollback $(date)"
    fi
    
    # Restaurar archivos desde backup
    if [ -f "$BACKUP_DIR/files_$TIMESTAMP.tar.gz" ]; then
        log "Extrayendo archivos desde backup..."
        tar -xzf "$BACKUP_DIR/files_$TIMESTAMP.tar.gz" --strip-components=0
        
        success "Código restaurado desde backup"
    else
        error "No se pudo encontrar el backup de archivos"
    fi
}

# Función para restaurar base de datos
restore_database() {
    log "🗄️ Restaurando bases de datos..."
    
    # Restaurar PostgreSQL
    if [ -f "$BACKUP_DIR/postgres_$TIMESTAMP.sql" ]; then
        if command -v psql &> /dev/null; then
            log "Restaurando PostgreSQL..."
            psql -h localhost -U evaltrack_user -d evaltrack_users < "$BACKUP_DIR/postgres_$TIMESTAMP.sql" || warning "Error restaurando PostgreSQL"
        else
            warning "psql no está instalado - no se puede restaurar PostgreSQL"
        fi
    else
        warning "No se encontró backup de PostgreSQL"
    fi
    
    # Restaurar MySQL
    if [ -f "$BACKUP_DIR/mysql_$TIMESTAMP.sql" ]; then
        if command -v mysql &> /dev/null; then
            log "Restaurando MySQL..."
            mysql -h localhost -u evaltrack_user -p evaltrack_business < "$BACKUP_DIR/mysql_$TIMESTAMP.sql" || warning "Error restaurando MySQL"
        else
            warning "mysql no está instalado - no se puede restaurar MySQL"
        fi
    else
        warning "No se encontró backup de MySQL"
    fi
    
    success "Bases de datos restauradas"
}

# Función para limpiar caché y archivos temporales
clean_cache() {
    log "🧹 Limpiando caché y archivos temporales..."
    
    # Limpiar caché de Laravel
    php artisan cache:clear
    php artisan config:clear
    php artisan view:clear
    php artisan route:clear
    
    # Limpiar caché de Composer
    composer dump-autoload --optimize
    
    # Limpiar archivos temporales
    rm -rf bootstrap/cache/*.php
    rm -rf storage/framework/cache/data/*
    rm -rf storage/framework/views/*
    
    success "Caché limpiado"
}

# Función para reinstalar dependencias
reinstall_dependencies() {
    log "📦 Reinstalando dependencias..."
    
    # Reinstalar dependencias PHP
    if [ -f "composer.json" ]; then
        composer install --no-dev --optimize-autoloader --no-interaction
        success "Dependencias PHP reinstaladas"
    fi
    
    # Reinstalar dependencias Node.js
    if [ -f "package.json" ]; then
        npm ci --production --silent
        success "Dependencias Node.js reinstaladas"
    fi
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

# Función para health check post-rollback
health_check() {
    log "🏥 Ejecutando health check post-rollback..."
    
    # Esperar un momento para que la aplicación se estabilice
    sleep 10
    
    # Verificar que la aplicación responde
    if curl -f http://localhost/health > /dev/null 2>&1; then
        success "Health check exitoso después del rollback"
        return 0
    else
        warning "Health check falló después del rollback"
        return 1
    fi
}

# Función para notificar resultado del rollback
notify_rollback_result() {
    local status=$1
    local message=$2
    
    log "📧 Notificando resultado del rollback: $status"
    
    # Aquí puedes agregar notificaciones por email, Slack, etc.
    # Por ahora solo lo guardamos en el log
    
    if [ "$status" = "SUCCESS" ]; then
        success "Rollback completado exitosamente"
    else
        error "Rollback falló: $message"
    fi
}

# Función para mostrar información del rollback
show_rollback_info() {
    log "📋 Información del rollback:"
    log "  📅 Timestamp del backup: $TIMESTAMP"
    log "  📁 Directorio de backup: $BACKUP_DIR"
    log "  📄 Log del rollback: $LOG_FILE"
    
    if [ -f "$BACKUP_DIR/files_$TIMESTAMP.tar.gz" ]; then
        local size=$(du -h "$BACKUP_DIR/files_$TIMESTAMP.tar.gz" | cut -f1)
        log "  📦 Tamaño del backup: $size"
    fi
}

# Función principal de rollback
main() {
    local start_time=$(date +%s)
    
    log "🔄 Iniciando rollback al backup: $TIMESTAMP"
    
    # Verificar backup
    check_backup_exists
    
    # Mostrar información
    show_rollback_info
    
    # Confirmar rollback (opcional)
    if [ "$2" != "--force" ]; then
        echo -e "${YELLOW}¿Estás seguro de que quieres hacer rollback? (y/N)${NC}"
        read -r response
        if [[ ! "$response" =~ ^[Yy]$ ]]; then
            log "Rollback cancelado por el usuario"
            exit 0
        fi
    fi
    
    # Ejecutar pasos de rollback
    backup_current_state
    restore_code
    restore_database
    clean_cache
    reinstall_dependencies
    check_permissions
    
    # Health check
    if health_check; then
        local end_time=$(date +%s)
        local duration=$((end_time - start_time))
        
        success "🎉 Rollback completado exitosamente en ${duration} segundos!"
        log "🌐 La aplicación debería estar disponible en: http://localhost"
        
        notify_rollback_result "SUCCESS" "Rollback completado en ${duration}s"
    else
        error "❌ Rollback completado pero health check falló"
        notify_rollback_result "WARNING" "Rollback completado pero hay problemas"
    fi
}

# Función para listar backups disponibles
list_backups() {
    log "📋 Backups disponibles:"
    
    if [ ! -d "$BACKUP_DIR" ]; then
        error "No se encontró directorio de backups"
    fi
    
    local backups=$(ls -1 "$BACKUP_DIR"/files_*.tar.gz 2>/dev/null | sed 's/.*files_\(.*\)\.tar\.gz/\1/' | sort -r)
    
    if [ -z "$backups" ]; then
        warning "No se encontraron backups"
        exit 0
    fi
    
    echo "$backups" | while read -r backup; do
        local size=$(du -h "$BACKUP_DIR/files_${backup}.tar.gz" 2>/dev/null | cut -f1 || echo "N/A")
        local date=$(echo "$backup" | sed 's/\([0-9]\{8\}\)_\([0-9]\{6\}\)/\1 \2/' | sed 's/\([0-9]\{4\}\)\([0-9]\{2\}\)\([0-9]\{2\}\)/\1-\2-\3/' | sed 's/\([0-9]\{2\}\)\([0-9]\{2\}\)\([0-9]\{2\}\)/\1:\2:\3/')
        
        if [ "$backup" = "$(cat "$BACKUP_DIR/last_backup.txt" 2>/dev/null || echo "")" ]; then
            echo -e "  ${GREEN}* $backup ($date) - $size${NC}"
        else
            echo -e "    $backup ($date) - $size"
        fi
    done
}

# Manejar argumentos
case "${1:-}" in
    "list"|"-l"|"--list")
        list_backups
        exit 0
        ;;
    "help"|"-h"|"--help")
        echo "Uso: $0 [backup_timestamp] [--force]"
        echo "     $0 list"
        echo ""
        echo "Opciones:"
        echo "  backup_timestamp  Timestamp del backup a restaurar"
        echo "  --force          No pedir confirmación"
        echo "  list             Listar backups disponibles"
        echo "  help             Mostrar esta ayuda"
        exit 0
        ;;
    *)
        main "$@"
        ;;
esac 