#!/bin/sh

# Script de entrada para EvalTrack Docker Container
# Versión: 1.0.0

set -e

echo "🚀 Iniciando EvalTrack v1.0.0..."

# Función para esperar a que un servicio esté disponible
wait_for_service() {
    local host=$1
    local port=$2
    local service_name=$3
    local max_attempts=30
    local attempt=1

    echo "⏳ Esperando que $service_name esté disponible en $host:$port..."

    while [ $attempt -le $max_attempts ]; do
        if nc -z $host $port 2>/dev/null; then
            echo "✅ $service_name está disponible"
            return 0
        fi
        
        echo "   Intento $attempt/$max_attempts - $service_name no está listo..."
        sleep 2
        attempt=$((attempt + 1))
    done

    echo "❌ $service_name no está disponible después de $max_attempts intentos"
    return 1
}

# Función para verificar la conectividad de base de datos
check_database_connection() {
    echo "🔍 Verificando conectividad de base de datos..."
    
    # Verificar PostgreSQL
    if [ -n "$DB_PGSQL_HOST" ]; then
        wait_for_service $DB_PGSQL_HOST $DB_PGSQL_PORT "PostgreSQL"
    fi
    
    # Verificar MySQL
    if [ -n "$DB_HOST" ]; then
        wait_for_service $DB_HOST $DB_PORT "MySQL"
    fi
    
    # Verificar Redis
    if [ -n "$REDIS_HOST" ]; then
        wait_for_service $REDIS_HOST $REDIS_PORT "Redis"
    fi
}

# Función para configurar la aplicación
setup_application() {
    echo "⚙️ Configurando aplicación..."
    
    # Verificar si es la primera ejecución
    if [ ! -f "/var/www/html/storage/installed" ]; then
        echo "🆕 Primera ejecución detectada, configurando aplicación..."
        
        # Cambiar al directorio de la aplicación
        cd /var/www/html
        
        # Generar clave de aplicación si no existe
        if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
            echo "🔑 Generando clave de aplicación..."
            php artisan key:generate --force
        fi
        
        # Ejecutar migraciones
        echo "🗄️ Ejecutando migraciones..."
        php artisan migrate --force
        
        # Ejecutar seeders si es necesario
        if [ "$APP_ENV" = "local" ] || [ "$APP_ENV" = "development" ]; then
            echo "🌱 Ejecutando seeders..."
            php artisan db:seed --force
        fi
        
        # Optimizar para producción
        if [ "$APP_ENV" = "production" ]; then
            echo "🚀 Optimizando para producción..."
            php artisan config:cache
            php artisan route:cache
            php artisan view:cache
        fi
        
        # Marcar como instalado
        touch /var/www/html/storage/installed
        echo "✅ Aplicación configurada correctamente"
    else
        echo "✅ Aplicación ya configurada"
    fi
}

# Función para configurar permisos
setup_permissions() {
    echo "🔐 Configurando permisos..."
    
    # Cambiar al directorio de la aplicación
    cd /var/www/html
    
    # Configurar permisos para storage y cache
    chown -R www-data:www-data storage bootstrap/cache
    chmod -R 775 storage bootstrap/cache
    
    # Configurar permisos para logs
    chmod -R 775 storage/logs
    
    echo "✅ Permisos configurados"
}

# Función para crear health check endpoint
create_health_check() {
    echo "🏥 Configurando health check..."
    
    # Crear endpoint de health check si no existe
    if [ ! -f "/var/www/html/public/health" ]; then
        cat > /var/www/html/public/health << 'EOF'
<?php
header('Content-Type: application/json');

$health = [
    'status' => 'healthy',
    'timestamp' => date('Y-m-d H:i:s'),
    'version' => '1.0.0',
    'environment' => $_ENV['APP_ENV'] ?? 'unknown',
    'checks' => []
];

// Verificar conectividad de base de datos
try {
    if (isset($_ENV['DB_CONNECTION']) && $_ENV['DB_CONNECTION'] === 'pgsql') {
        $pdo = new PDO(
            "pgsql:host={$_ENV['DB_PGSQL_HOST']};port={$_ENV['DB_PGSQL_PORT']};dbname={$_ENV['DB_PGSQL_DATABASE']}",
            $_ENV['DB_PGSQL_USERNAME'],
            $_ENV['DB_PGSQL_PASSWORD']
        );
        $health['checks']['postgresql'] = 'connected';
    }
} catch (Exception $e) {
    $health['checks']['postgresql'] = 'error: ' . $e->getMessage();
    $health['status'] = 'unhealthy';
}

try {
    if (isset($_ENV['DB_HOST'])) {
        $pdo = new PDO(
            "mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_DATABASE']}",
            $_ENV['DB_USERNAME'],
            $_ENV['DB_PASSWORD']
        );
        $health['checks']['mysql'] = 'connected';
    }
} catch (Exception $e) {
    $health['checks']['mysql'] = 'error: ' . $e->getMessage();
    $health['status'] = 'unhealthy';
}

// Verificar Redis
try {
    if (isset($_ENV['REDIS_HOST'])) {
        $redis = new Redis();
        $redis->connect($_ENV['REDIS_HOST'], $_ENV['REDIS_PORT']);
        if (isset($_ENV['REDIS_PASSWORD'])) {
            $redis->auth($_ENV['REDIS_PASSWORD']);
        }
        $redis->ping();
        $health['checks']['redis'] = 'connected';
    }
} catch (Exception $e) {
    $health['checks']['redis'] = 'error: ' . $e->getMessage();
    $health['status'] = 'unhealthy';
}

// Verificar storage
if (is_writable('/var/www/html/storage')) {
    $health['checks']['storage'] = 'writable';
} else {
    $health['checks']['storage'] = 'not writable';
    $health['status'] = 'unhealthy';
}

http_response_code($health['status'] === 'healthy' ? 200 : 503);
echo json_encode($health, JSON_PRETTY_PRINT);
EOF
        echo "✅ Health check endpoint creado"
    fi
}

# Función para iniciar servicios
start_services() {
    echo "🚀 Iniciando servicios..."
    
    # Iniciar supervisor
    exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
}

# Función principal
main() {
    echo "=========================================="
    echo "   EvalTrack v1.0.0 - Sistema de Gestión"
    echo "   de Talento Humano"
    echo "=========================================="
    
    # Verificar variables de entorno críticas
    if [ -z "$APP_ENV" ]; then
        echo "❌ Error: APP_ENV no está definida"
        exit 1
    fi
    
    echo "🌍 Entorno: $APP_ENV"
    echo "🔗 URL: ${APP_URL:-'No configurada'}"
    
    # Verificar conectividad de servicios
    check_database_connection
    
    # Configurar aplicación
    setup_application
    
    # Configurar permisos
    setup_permissions
    
    # Crear health check
    create_health_check
    
    # Iniciar servicios
    start_services
}

# Ejecutar función principal
main "$@" 