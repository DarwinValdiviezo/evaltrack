#!/bin/bash

# Script de despliegue para EvalTrack con XAMPP (sin Docker)
# Uso: ./scripts/deploy-xampp.sh [environment]

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
LOG_FILE="storage/logs/deploy-xampp-$(date +%Y%m%d_%H%M%S).log"
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

# Crear directorios necesarios
mkdir -p storage/logs
mkdir -p $BACKUP_DIR

log "🚀 Iniciando despliegue de EvalTrack con XAMPP en entorno: $ENVIRONMENT"

# Función para verificar servicios XAMPP
check_xampp_services() {
    log "🔍 Verificando servicios XAMPP..."
    
    # Verificar Apache
    if curl -f http://localhost > /dev/null 2>&1; then
        success "Apache está ejecutándose"
    else
        error "Apache no está ejecutándose. Inicia XAMPP Control Panel y activa Apache."
    fi
    
    # Verificar MySQL
    if command -v mysql &> /dev/null; then
        if mysql -u root -e "SELECT 1;" > /dev/null 2>&1; then
            success "MySQL está ejecutándose"
        else
            error "MySQL no está ejecutándose. Inicia XAMPP Control Panel y activa MySQL."
        fi
    else
        error "MySQL no está instalado o no está en el PATH"
    fi
    
    # Verificar PHP
    if command -v php &> /dev/null; then
        local php_version=$(php -v | head -n1 | cut -d' ' -f2)
        success "PHP está disponible: $php_version"
    else
        error "PHP no está instalado o no está en el PATH"
    fi
}

# Función para crear bases de datos
create_databases() {
    log "🗄️ Creando bases de datos..."
    
    # Crear base de datos PostgreSQL (si está disponible)
    if command -v psql &> /dev/null; then
        log "Creando base de datos PostgreSQL..."
        psql -U postgres -c "CREATE DATABASE evaltrack_users;" 2>/dev/null || warning "Base de datos PostgreSQL ya existe o no se pudo crear"
        psql -U postgres -c "CREATE USER evaltrack_user WITH PASSWORD 'password';" 2>/dev/null || warning "Usuario PostgreSQL ya existe"
        psql -U postgres -c "GRANT ALL PRIVILEGES ON DATABASE evaltrack_users TO evaltrack_user;" 2>/dev/null || warning "No se pudieron otorgar privilegios PostgreSQL"
    else
        warning "PostgreSQL no está disponible - usando solo MySQL"
    fi
    
    # Crear base de datos MySQL
    log "Creando base de datos MySQL..."
    mysql -u root -e "CREATE DATABASE IF NOT EXISTS evaltrack_business;" || error "No se pudo crear base de datos MySQL"
    mysql -u root -e "CREATE USER IF NOT EXISTS 'evaltrack_user'@'localhost' IDENTIFIED BY 'password';" || warning "Usuario MySQL ya existe"
    mysql -u root -e "GRANT ALL PRIVILEGES ON evaltrack_business.* TO 'evaltrack_user'@'localhost';" || warning "No se pudieron otorgar privilegios MySQL"
    mysql -u root -e "FLUSH PRIVILEGES;" || warning "No se pudieron actualizar privilegios MySQL"
    
    success "Bases de datos configuradas"
}

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
    
    # Backup de base de datos MySQL
    if command -v mysqldump &> /dev/null; then
        mysqldump -u root evaltrack_business > "$BACKUP_DIR/mysql_$TIMESTAMP.sql" 2>/dev/null || warning "No se pudo hacer backup de MySQL"
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
    
    # Configurar variables específicas para XAMPP
    sed -i 's/DB_PGSQL_HOST=127.0.0.1/DB_PGSQL_HOST=localhost/' .env
    sed -i 's/DB_HOST=127.0.0.1/DB_HOST=localhost/' .env
    sed -i 's/DB_USERNAME=evaltrack_user/DB_USERNAME=root/' .env
    sed -i 's/DB_PASSWORD=password/DB_PASSWORD=/' .env
    
    # Generar clave de aplicación
    php artisan key:generate --force
    
    # Limpiar caché
    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear
    php artisan route:clear
    
    success "Entorno configurado para XAMPP"
}

# Función para migrar base de datos
migrate_database() {
    log "🗄️ Ejecutando migraciones..."
    
    # Migrar base de datos de usuarios (PostgreSQL si está disponible)
    if command -v psql &> /dev/null; then
        php artisan migrate --database=pgsql --path=database/migrations/users --force || warning "Error en migración PostgreSQL"
    else
        log "PostgreSQL no disponible - saltando migraciones de usuarios"
    fi
    
    # Migrar base de datos de negocio (MySQL)
    php artisan migrate --database=mysql_business --path=database/migrations/business --force || error "Error en migración MySQL"
    
    success "Migraciones completadas"
}

# Función para ejecutar seeders
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
    
    # Establecer permisos (Windows-friendly)
    if [[ "$OSTYPE" == "msys" || "$OSTYPE" == "win32" ]]; then
        # Windows - no cambiar permisos
        log "Sistema Windows detectado - saltando cambio de permisos"
    else
        # Linux/Mac
        chmod -R 775 storage
        chmod -R 775 bootstrap/cache
    fi
    
    success "Permisos configurados"
}

# Función para health check
health_check() {
    log "🏥 Ejecutando health check..."
    
    # Esperar un momento para que la aplicación se estabilice
    sleep 5
    
    # Verificar que la aplicación responde
    if curl -f http://localhost/evaltrack/public/health > /dev/null 2>&1; then
        success "Health check exitoso"
    elif curl -f http://localhost/health > /dev/null 2>&1; then
        success "Health check exitoso"
    else
        warning "Health check falló - verifica que Apache esté ejecutándose y la aplicación esté en el directorio correcto"
        return 1
    fi
}

# Función para configurar Apache (opcional)
setup_apache() {
    log "🌐 Configurando Apache..."
    
    # Crear archivo .htaccess si no existe
    if [ ! -f "public/.htaccess" ]; then
        cat > "public/.htaccess" << 'EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
EOF
        success "Archivo .htaccess creado"
    fi
    
    # Instrucciones para configurar virtual host
    log "📋 Para configurar un virtual host, agrega esto a tu httpd-vhosts.conf:"
    echo ""
    echo "    <VirtualHost *:80>"
    echo "        DocumentRoot \"$(pwd)/public\""
    echo "        ServerName evaltrack.local"
    echo "        <Directory \"$(pwd)/public\">"
    echo "            AllowOverride All"
    echo "            Require all granted"
    echo "        </Directory>"
    echo "    </VirtualHost>"
    echo ""
    echo "Y agrega '127.0.0.1 evaltrack.local' a tu archivo hosts"
    echo ""
}

# Función para mostrar instrucciones de acceso
show_access_instructions() {
    log "🌐 Instrucciones de acceso:"
    echo ""
    echo "1. Asegúrate de que Apache y MySQL estén ejecutándose en XAMPP"
    echo "2. Accede a la aplicación en:"
    echo "   - http://localhost/evaltrack/public"
    echo "   - http://localhost (si configuras virtual host)"
    echo "3. Health check: http://localhost/evaltrack/public/health"
    echo ""
    echo "Credenciales por defecto:"
    echo "   - Usuario: admin@evaltrack.com"
    echo "   - Contraseña: password"
    echo ""
}

# Función principal de despliegue
main() {
    local start_time=$(date +%s)
    
    case $ENVIRONMENT in
        "local"|"development"|"staging"|"production")
            log "Iniciando despliegue en entorno: $ENVIRONMENT"
            
            # Ejecutar pasos de despliegue
            check_xampp_services
            create_databases
            backup_automatic
            update_code
            install_dependencies
            setup_environment
            check_permissions
            migrate_database
            run_seeders
            optimize_application
            setup_apache
            
            # Health check
            if ! health_check; then
                warning "Health check falló - verifica la configuración"
            fi
            
            local end_time=$(date +%s)
            local duration=$((end_time - start_time))
            
            success "🎉 Despliegue completado en ${duration} segundos!"
            
            # Mostrar instrucciones
            show_access_instructions
            
            ;;
        *)
            error "Entorno no válido. Usa: local, development, staging, o production"
            ;;
    esac
}

# Ejecutar función principal
main "$@" 