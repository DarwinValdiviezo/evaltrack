#!/bin/bash

# Script de backup para EvalTrack
# Uso: ./scripts/backup-only.sh [backup_name]

set -e

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Variables
BACKUP_DIR="backup"
BACKUP_NAME=${1:-"auto_$(date +%Y%m%d_%H%M%S)"}
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
LOG_FILE="storage/logs/backup-$(date +%Y%m%d_%H%M%S).log"

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

# Crear directorios necesarios
mkdir -p storage/logs
mkdir -p $BACKUP_DIR

log "📦 Iniciando backup de EvalTrack: $BACKUP_NAME"

# Función para backup de archivos
backup_files() {
    log "📁 Creando backup de archivos..."
    
    local backup_file="$BACKUP_DIR/files_${BACKUP_NAME}.tar.gz"
    
    # Crear backup de archivos excluyendo directorios innecesarios
    tar -czf "$backup_file" \
        --exclude='vendor' \
        --exclude='node_modules' \
        --exclude='storage/logs' \
        --exclude='storage/framework/cache' \
        --exclude='storage/framework/sessions' \
        --exclude='storage/framework/views' \
        --exclude='.git' \
        --exclude='backup' \
        --exclude='.phpunit.cache' \
        .
    
    local size=$(du -h "$backup_file" | cut -f1)
    success "Backup de archivos creado: $backup_file ($size)"
}

# Función para backup de base de datos PostgreSQL
backup_postgresql() {
    log "🗄️ Creando backup de PostgreSQL..."
    
    local backup_file="$BACKUP_DIR/postgres_${BACKUP_NAME}.sql"
    
    if command -v pg_dump &> /dev/null; then
        # Intentar backup con diferentes métodos de autenticación
        if pg_dump -h localhost -U evaltrack_user evaltrack_users > "$backup_file" 2>/dev/null; then
            local size=$(du -h "$backup_file" | cut -f1)
            success "Backup PostgreSQL creado: $backup_file ($size)"
        elif pg_dump -h localhost evaltrack_users > "$backup_file" 2>/dev/null; then
            local size=$(du -h "$backup_file" | cut -f1)
            success "Backup PostgreSQL creado (sin usuario): $backup_file ($size)"
        else
            warning "No se pudo crear backup de PostgreSQL - verifica credenciales"
            rm -f "$backup_file"
        fi
    else
        warning "pg_dump no está instalado - no se puede hacer backup de PostgreSQL"
    fi
}

# Función para backup de base de datos MySQL
backup_mysql() {
    log "🗄️ Creando backup de MySQL..."
    
    local backup_file="$BACKUP_DIR/mysql_${BACKUP_NAME}.sql"
    
    if command -v mysqldump &> /dev/null; then
        # Intentar backup con diferentes métodos de autenticación
        if mysqldump -h localhost -u evaltrack_user -p evaltrack_business > "$backup_file" 2>/dev/null; then
            local size=$(du -h "$backup_file" | cut -f1)
            success "Backup MySQL creado: $backup_file ($size)"
        elif mysqldump -h localhost evaltrack_business > "$backup_file" 2>/dev/null; then
            local size=$(du -h "$backup_file" | cut -f1)
            success "Backup MySQL creado (sin usuario): $backup_file ($size)"
        else
            warning "No se pudo crear backup de MySQL - verifica credenciales"
            rm -f "$backup_file"
        fi
    else
        warning "mysqldump no está instalado - no se puede hacer backup de MySQL"
    fi
}

# Función para backup de configuración
backup_config() {
    log "⚙️ Creando backup de configuración..."
    
    local config_backup="$BACKUP_DIR/config_${BACKUP_NAME}.tar.gz"
    
    # Backup de archivos de configuración críticos
    tar -czf "$config_backup" \
        .env \
        config/ \
        docker-compose.yml \
        Dockerfile \
        composer.json \
        composer.lock \
        package.json \
        package-lock.json \
        2>/dev/null || warning "No se pudo crear backup de configuración"
    
    if [ -f "$config_backup" ]; then
        local size=$(du -h "$config_backup" | cut -f1)
        success "Backup de configuración creado: $config_backup ($size)"
    fi
}

# Función para backup de logs importantes
backup_logs() {
    log "📋 Creando backup de logs importantes..."
    
    local logs_backup="$BACKUP_DIR/logs_${BACKUP_NAME}.tar.gz"
    
    # Backup de logs de los últimos 7 días
    find storage/logs -name "*.log" -mtime -7 -exec tar -czf "$logs_backup" {} + 2>/dev/null || warning "No se pudo crear backup de logs"
    
    if [ -f "$logs_backup" ]; then
        local size=$(du -h "$logs_backup" | cut -f1)
        success "Backup de logs creado: $logs_backup ($size)"
    fi
}

# Función para crear archivo de metadatos
create_metadata() {
    log "📄 Creando archivo de metadatos..."
    
    local metadata_file="$BACKUP_DIR/metadata_${BACKUP_NAME}.json"
    
    cat > "$metadata_file" << EOF
{
    "backup_name": "$BACKUP_NAME",
    "timestamp": "$TIMESTAMP",
    "created_at": "$(date -Iseconds)",
    "version": "1.0.0",
    "environment": "$(php artisan env 2>/dev/null || echo 'unknown')",
    "laravel_version": "$(php artisan --version 2>/dev/null || echo 'unknown')",
    "php_version": "$(php -v | head -n1 | cut -d' ' -f2 2>/dev/null || echo 'unknown')",
    "files": [
        "$(ls -1 $BACKUP_DIR/*${BACKUP_NAME}* 2>/dev/null | sed 's/.*\//"/;s/$/"/' | tr '\n' ',' | sed 's/,$//')"
    ],
    "total_size": "$(du -sh $BACKUP_DIR/*${BACKUP_NAME}* 2>/dev/null | awk '{sum+=$1} END {print sum "K"}')",
    "git_commit": "$(git rev-parse HEAD 2>/dev/null || echo 'unknown')",
    "git_branch": "$(git branch --show-current 2>/dev/null || echo 'unknown')"
}
EOF
    
    success "Metadatos creados: $metadata_file"
}

# Función para limpiar backups antiguos
cleanup_old_backups() {
    log "🧹 Limpiando backups antiguos..."
    
    # Mantener solo los últimos 10 backups
    local backup_count=$(ls -1 $BACKUP_DIR/files_*.tar.gz 2>/dev/null | wc -l)
    
    if [ $backup_count -gt 10 ]; then
        local files_to_delete=$(ls -1t $BACKUP_DIR/files_*.tar.gz | tail -n +11)
        
        for file in $files_to_delete; do
            local backup_name=$(basename "$file" .tar.gz | sed 's/files_//')
            log "Eliminando backup antiguo: $backup_name"
            
            # Eliminar todos los archivos relacionados con este backup
            rm -f $BACKUP_DIR/*${backup_name}*
        done
        
        success "Backups antiguos eliminados"
    else
        log "No hay backups antiguos para eliminar"
    fi
}

# Función para verificar integridad del backup
verify_backup() {
    log "🔍 Verificando integridad del backup..."
    
    local files_backup="$BACKUP_DIR/files_${BACKUP_NAME}.tar.gz"
    
    if [ -f "$files_backup" ]; then
        if tar -tzf "$files_backup" > /dev/null 2>&1; then
            success "Backup de archivos verificado correctamente"
        else
            error "Backup de archivos corrupto"
        fi
    fi
    
    # Verificar archivos SQL si existen
    local postgres_backup="$BACKUP_DIR/postgres_${BACKUP_NAME}.sql"
    if [ -f "$postgres_backup" ]; then
        if head -n1 "$postgres_backup" | grep -q "PostgreSQL database dump"; then
            success "Backup PostgreSQL verificado"
        else
            warning "Backup PostgreSQL puede estar corrupto"
        fi
    fi
    
    local mysql_backup="$BACKUP_DIR/mysql_${BACKUP_NAME}.sql"
    if [ -f "$mysql_backup" ]; then
        if head -n1 "$mysql_backup" | grep -q "MySQL dump"; then
            success "Backup MySQL verificado"
        else
            warning "Backup MySQL puede estar corrupto"
        fi
    fi
}

# Función para mostrar resumen del backup
show_backup_summary() {
    log "📊 Resumen del backup:"
    log "  📅 Nombre: $BACKUP_NAME"
    log "  🕐 Timestamp: $TIMESTAMP"
    log "  📁 Directorio: $BACKUP_DIR"
    
    # Listar archivos creados
    local backup_files=$(ls -1 $BACKUP_DIR/*${BACKUP_NAME}* 2>/dev/null)
    if [ -n "$backup_files" ]; then
        log "  📦 Archivos creados:"
        echo "$backup_files" | while read -r file; do
            local size=$(du -h "$file" | cut -f1)
            local filename=$(basename "$file")
            log "    - $filename ($size)"
        done
    fi
    
    # Tamaño total
    local total_size=$(du -sh $BACKUP_DIR/*${BACKUP_NAME}* 2>/dev/null | awk '{sum+=$1} END {print sum "K"}' || echo "N/A")
    log "  💾 Tamaño total: $total_size"
}

# Función para actualizar backup más reciente
update_latest_backup() {
    echo "$BACKUP_NAME" > "$BACKUP_DIR/last_backup.txt"
    success "Backup marcado como más reciente"
}

# Función principal
main() {
    local start_time=$(date +%s)
    
    log "🚀 Iniciando proceso de backup completo..."
    
    # Ejecutar todos los pasos de backup
    backup_files
    backup_postgresql
    backup_mysql
    backup_config
    backup_logs
    create_metadata
    verify_backup
    cleanup_old_backups
    update_latest_backup
    
    local end_time=$(date +%s)
    local duration=$((end_time - start_time))
    
    success "🎉 Backup completado exitosamente en ${duration} segundos!"
    
    # Mostrar resumen
    show_backup_summary
    
    log "📄 Log completo guardado en: $LOG_FILE"
}

# Función para listar backups existentes
list_backups() {
    log "📋 Backups existentes:"
    
    if [ ! -d "$BACKUP_DIR" ]; then
        error "No se encontró directorio de backups"
    fi
    
    local backups=$(ls -1 $BACKUP_DIR/files_*.tar.gz 2>/dev/null | sed 's/.*files_\(.*\)\.tar\.gz/\1/' | sort -r)
    
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
        echo "Uso: $0 [backup_name]"
        echo "     $0 list"
        echo ""
        echo "Opciones:"
        echo "  backup_name  Nombre personalizado para el backup (opcional)"
        echo "  list         Listar backups existentes"
        echo "  help         Mostrar esta ayuda"
        echo ""
        echo "Si no se especifica nombre, se usa: auto_YYYYMMDD_HHMMSS"
        exit 0
        ;;
    *)
        main "$@"
        ;;
esac 