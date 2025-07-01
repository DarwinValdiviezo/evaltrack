#!/bin/bash

# Script de despliegue para EvalTrack
# Uso: ./scripts/deploy.sh [environment]

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
ENVIRONMENT=${1:-local}
PROJECT_NAME="evaltrack"
DOCKER_IMAGE="company/evaltrack:latest"

# Verificar que estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    error "No se encontró el archivo artisan. Ejecuta este script desde la raíz del proyecto."
fi

log "🚀 Iniciando despliegue de EvalTrack en entorno: $ENVIRONMENT"

# Función para backup de base de datos
backup_database() {
    log "📦 Creando backup de base de datos..."
    
    if [ "$ENVIRONMENT" = "production" ]; then
        # Backup PostgreSQL
        if command -v pg_dump &> /dev/null; then
            pg_dump -h localhost -U evaltrack_user evaltrack_users > "backup/postgres_$(date +%Y%m%d_%H%M%S).sql"
            success "Backup PostgreSQL creado"
        fi
        
        # Backup MySQL
        if command -v mysqldump &> /dev/null; then
            mysqldump -h localhost -u evaltrack_user -p evaltrack_business > "backup/mysql_$(date +%Y%m%d_%H%M%S).sql"
            success "Backup MySQL creado"
        fi
    fi
}

# Función para instalar dependencias
install_dependencies() {
    log "📦 Instalando dependencias..."
    
    # Instalar dependencias PHP
    if [ -f "composer.json" ]; then
        composer install --no-dev --optimize-autoloader
        success "Dependencias PHP instaladas"
    fi
    
    # Instalar dependencias Node.js
    if [ -f "package.json" ]; then
        npm ci --production
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
    php artisan migrate --database=pgsql --path=database/migrations/users --force
    
    # Migrar base de datos de negocio (MySQL)
    php artisan migrate --database=mysql_business --path=database/migrations/business --force
    
    success "Migraciones completadas"
}

# Función para ejecutar seeders
run_seeders() {
    log "🌱 Ejecutando seeders..."
    
    if [ "$ENVIRONMENT" = "local" ] || [ "$ENVIRONMENT" = "development" ]; then
        php artisan db:seed --force
        success "Seeders ejecutados"
    else
        warning "Saltando seeders en entorno $ENVIRONMENT"
    fi
}

# Función para optimizar aplicación
optimize_application() {
    log "⚡ Optimizando aplicación..."
    
    # Compilar assets
    npm run build
    
    # Optimizar configuración
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    # Optimizar autoloader
    composer dump-autoload --optimize
    
    success "Aplicación optimizada"
}

# Función para verificar permisos
check_permissions() {
    log "🔐 Verificando permisos..."
    
    # Crear directorios necesarios
    mkdir -p bootstrap/cache
    mkdir -p storage/framework/{cache,sessions,views}
    mkdir -p storage/logs
    mkdir -p backup
    
    # Establecer permisos
    chmod -R 775 storage
    chmod -R 775 bootstrap/cache
    
    success "Permisos configurados"
}

# Función para health check
health_check() {
    log "🏥 Ejecutando health check..."
    
    # Verificar que la aplicación responde
    if curl -f http://localhost/health > /dev/null 2>&1; then
        success "Health check exitoso"
    else
        warning "Health check falló - verifica que el servidor esté ejecutándose"
    fi
}

# Función para despliegue con Docker
deploy_docker() {
    log "🐳 Desplegando con Docker..."
    
    # Construir imagen
    docker build -t $DOCKER_IMAGE .
    
    # Detener contenedores existentes
    docker-compose down
    
    # Levantar nuevos contenedores
    docker-compose up -d
    
    success "Despliegue Docker completado"
}

# Función para despliegue tradicional
deploy_traditional() {
    log "🖥️ Desplegando aplicación tradicional..."
    
    # Backup
    backup_database
    
    # Instalar dependencias
    install_dependencies
    
    # Configurar entorno
    setup_environment
    
    # Verificar permisos
    check_permissions
    
    # Migrar base de datos
    migrate_database
    
    # Ejecutar seeders
    run_seeders
    
    # Optimizar aplicación
    optimize_application
    
    # Health check
    health_check
    
    success "Despliegue tradicional completado"
}

# Función principal de despliegue
main() {
    case $ENVIRONMENT in
        "local"|"development"|"staging"|"production")
            log "Iniciando despliegue en entorno: $ENVIRONMENT"
            
            if [ "$ENVIRONMENT" = "local" ] && command -v docker &> /dev/null; then
                deploy_docker
            else
                deploy_traditional
            fi
            
            success "🎉 Despliegue completado exitosamente!"
            log "🌐 La aplicación debería estar disponible en: http://localhost"
            ;;
        *)
            error "Entorno no válido. Usa: local, development, staging, o production"
            ;;
    esac
}

# Ejecutar función principal
main "$@" 