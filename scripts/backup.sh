#!/bin/bash

# Script de backup para EvalTrack
# Uso: ./scripts/backup.sh [backup_type]

set -e

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

error() {
    echo -e "${RED}[ERROR]${NC} $1"
    exit 1
}

success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

# Variables
BACKUP_TYPE=${1:-full}
BACKUP_DIR="backups"
DATE=$(date +%Y%m%d_%H%M%S)
RETENTION_DAYS=30

# Configuración de bases de datos
POSTGRES_HOST=${POSTGRES_HOST:-localhost}
POSTGRES_PORT=${POSTGRES_PORT:-5432}
POSTGRES_DB=${POSTGRES_DB:-evaltrack_users}
POSTGRES_USER=${POSTGRES_USER:-evaltrack_user}
POSTGRES_PASSWORD=${POSTGRES_PASSWORD:-password}

MYSQL_HOST=${MYSQL_HOST:-localhost}
MYSQL_PORT=${MYSQL_PORT:-3306}
MYSQL_DB=${MYSQL_DB:-evaltrack_business}
MYSQL_USER=${MYSQL_USER:-evaltrack_user}
MYSQL_PASSWORD=${MYSQL_PASSWORD:-password}

# Crear directorio de backup si no existe
mkdir -p "$BACKUP_DIR"

log "📦 Iniciando backup tipo: $BACKUP_TYPE"

# Función para backup PostgreSQL
backup_postgres() {
    log "🗄️ Creando backup de PostgreSQL..."
    
    local filename="postgres_${DATE}.sql"
    local filepath="$BACKUP_DIR/$filename"
    
    if command -v pg_dump &> /dev/null; then
        PGPASSWORD="$POSTGRES_PASSWORD" pg_dump \
            -h "$POSTGRES_HOST" \
            -p "$POSTGRES_PORT" \
            -U "$POSTGRES_USER" \
            -d "$POSTGRES_DB" \
            --verbose \
            --clean \
            --if-exists \
            --create \
            > "$filepath"
        
        # Comprimir backup
        gzip "$filepath"
        
        success "Backup PostgreSQL creado: ${filepath}.gz"
        echo "$filepath.gz" >> "$BACKUP_DIR/postgres_backups.txt"
    else
        error "pg_dump no está instalado"
    fi
}

# Función para backup MySQL
backup_mysql() {
    log "🗄️ Creando backup de MySQL..."
    
    local filename="mysql_${DATE}.sql"
    local filepath="$BACKUP_DIR/$filename"
    
    if command -v mysqldump &> /dev/null; then
        mysqldump \
            -h "$MYSQL_HOST" \
            -P "$MYSQL_PORT" \
            -u "$MYSQL_USER" \
            -p"$MYSQL_PASSWORD" \
            "$MYSQL_DB" \
            --single-transaction \
            --routines \
            --triggers \
            --verbose \
            > "$filepath"
        
        # Comprimir backup
        gzip "$filepath"
        
        success "Backup MySQL creado: ${filepath}.gz"
        echo "$filepath.gz" >> "$BACKUP_DIR/mysql_backups.txt"
    else
        error "mysqldump no está instalado"
    fi
}

# Función para backup de archivos
backup_files() {
    log "📁 Creando backup de archivos..."
    
    local filename="files_${DATE}.tar.gz"
    local filepath="$BACKUP_DIR/$filename"
    
    # Backup de directorios importantes
    tar -czf "$filepath" \
        --exclude='vendor' \
        --exclude='node_modules' \
        --exclude='storage/logs/*' \
        --exclude='storage/framework/cache/*' \
        --exclude='storage/framework/sessions/*' \
        --exclude='storage/framework/views/*' \
        --exclude='.git' \
        --exclude='backups' \
        .
    
    success "Backup de archivos creado: $filepath"
    echo "$filepath" >> "$BACKUP_DIR/files_backups.txt"
}

# Función para backup completo
backup_full() {
    log "🔄 Iniciando backup completo..."
    
    backup_postgres
    backup_mysql
    backup_files
    
    success "Backup completo finalizado"
}

# Función para limpiar backups antiguos
cleanup_old_backups() {
    log "🧹 Limpiando backups antiguos (más de $RETENTION_DAYS días)..."
    
    # Limpiar backups PostgreSQL
    if [ -f "$BACKUP_DIR/postgres_backups.txt" ]; then
        while IFS= read -r file; do
            if [ -f "$file" ]; then
                file_age=$(( ($(date +%s) - $(stat -c %Y "$file")) / 86400 ))
                if [ $file_age -gt $RETENTION_DAYS ]; then
                    rm "$file"
                    log "Eliminado backup PostgreSQL antiguo: $file"
                fi
            fi
        done < "$BACKUP_DIR/postgres_backups.txt"
    fi
    
    # Limpiar backups MySQL
    if [ -f "$BACKUP_DIR/mysql_backups.txt" ]; then
        while IFS= read -r file; do
            if [ -f "$file" ]; then
                file_age=$(( ($(date +%s) - $(stat -c %Y "$file")) / 86400 ))
                if [ $file_age -gt $RETENTION_DAYS ]; then
                    rm "$file"
                    log "Eliminado backup MySQL antiguo: $file"
                fi
            fi
        done < "$BACKUP_DIR/mysql_backups.txt"
    fi
    
    # Limpiar backups de archivos
    if [ -f "$BACKUP_DIR/files_backups.txt" ]; then
        while IFS= read -r file; do
            if [ -f "$file" ]; then
                file_age=$(( ($(date +%s) - $(stat -c %Y "$file")) / 86400 ))
                if [ $file_age -gt $RETENTION_DAYS ]; then
                    rm "$file"
                    log "Eliminado backup de archivos antiguo: $file"
                fi
            fi
        done < "$BACKUP_DIR/files_backups.txt"
    fi
    
    success "Limpieza de backups completada"
}

# Función para listar backups
list_backups() {
    log "📋 Listando backups disponibles..."
    
    echo "Backups PostgreSQL:"
    if [ -f "$BACKUP_DIR/postgres_backups.txt" ]; then
        while IFS= read -r file; do
            if [ -f "$file" ]; then
                size=$(du -h "$file" | cut -f1)
                date=$(stat -c %y "$file" | cut -d' ' -f1)
                echo "  $file ($size, $date)"
            fi
        done < "$BACKUP_DIR/postgres_backups.txt"
    else
        echo "  No hay backups PostgreSQL"
    fi
    
    echo ""
    echo "Backups MySQL:"
    if [ -f "$BACKUP_DIR/mysql_backups.txt" ]; then
        while IFS= read -r file; do
            if [ -f "$file" ]; then
                size=$(du -h "$file" | cut -f1)
                date=$(stat -c %y "$file" | cut -d' ' -f1)
                echo "  $file ($size, $date)"
            fi
        done < "$BACKUP_DIR/mysql_backups.txt"
    else
        echo "  No hay backups MySQL"
    fi
    
    echo ""
    echo "Backups de archivos:"
    if [ -f "$BACKUP_DIR/files_backups.txt" ]; then
        while IFS= read -r file; do
            if [ -f "$file" ]; then
                size=$(du -h "$file" | cut -f1)
                date=$(stat -c %y "$file" | cut -d' ' -f1)
                echo "  $file ($size, $date)"
            fi
        done < "$BACKUP_DIR/files_backups.txt"
    else
        echo "  No hay backups de archivos"
    fi
}

# Función para restaurar backup
restore_backup() {
    local backup_file=$2
    
    if [ -z "$backup_file" ]; then
        error "Debes especificar el archivo de backup a restaurar"
    fi
    
    if [ ! -f "$backup_file" ]; then
        error "Archivo de backup no encontrado: $backup_file"
    fi
    
    log "🔄 Restaurando backup: $backup_file"
    
    # Determinar tipo de backup por extensión
    if [[ "$backup_file" == *"postgres"* ]]; then
        log "Restaurando backup PostgreSQL..."
        gunzip -c "$backup_file" | PGPASSWORD="$POSTGRES_PASSWORD" psql \
            -h "$POSTGRES_HOST" \
            -p "$POSTGRES_PORT" \
            -U "$POSTGRES_USER" \
            -d "$POSTGRES_DB"
        success "Backup PostgreSQL restaurado"
    elif [[ "$backup_file" == *"mysql"* ]]; then
        log "Restaurando backup MySQL..."
        gunzip -c "$backup_file" | mysql \
            -h "$MYSQL_HOST" \
            -P "$MYSQL_PORT" \
            -u "$MYSQL_USER" \
            -p"$MYSQL_PASSWORD" \
            "$MYSQL_DB"
        success "Backup MySQL restaurado"
    elif [[ "$backup_file" == *"files"* ]]; then
        log "Restaurando backup de archivos..."
        tar -xzf "$backup_file" -C /tmp
        success "Backup de archivos restaurado en /tmp"
    else
        error "Tipo de backup no reconocido"
    fi
}

# Función principal
main() {
    case $BACKUP_TYPE in
        "full")
            backup_full
            ;;
        "postgres")
            backup_postgres
            ;;
        "mysql")
            backup_mysql
            ;;
        "files")
            backup_files
            ;;
        "cleanup")
            cleanup_old_backups
            ;;
        "list")
            list_backups
            ;;
        "restore")
            restore_backup "$@"
            ;;
        *)
            echo "Uso: $0 [full|postgres|mysql|files|cleanup|list|restore]"
            echo ""
            echo "Opciones:"
            echo "  full     - Backup completo (bases de datos + archivos)"
            echo "  postgres - Solo backup PostgreSQL"
            echo "  mysql    - Solo backup MySQL"
            echo "  files    - Solo backup de archivos"
            echo "  cleanup  - Limpiar backups antiguos"
            echo "  list     - Listar backups disponibles"
            echo "  restore  - Restaurar backup específico"
            exit 1
            ;;
    esac
    
    # Limpiar backups antiguos después de crear nuevos
    if [ "$BACKUP_TYPE" != "cleanup" ] && [ "$BACKUP_TYPE" != "list" ] && [ "$BACKUP_TYPE" != "restore" ]; then
        cleanup_old_backups
    fi
    
    success "🎉 Proceso de backup completado"
}

# Ejecutar función principal
main "$@" 