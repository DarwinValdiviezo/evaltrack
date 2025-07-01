#!/bin/bash

# Script para probar localmente antes de hacer push
# Uso: ./scripts/test-local.sh

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

log "🧪 Iniciando pruebas locales..."

# Verificar que estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    error "No se encontró el archivo artisan. Ejecuta este script desde la raíz del proyecto."
fi

# Verificar dependencias
log "📦 Verificando dependencias..."
if [ ! -d "vendor" ]; then
    log "Instalando dependencias PHP..."
    composer install
fi

if [ ! -d "node_modules" ]; then
    log "Instalando dependencias Node.js..."
    npm install
fi

# Configurar entorno de testing
log "⚙️ Configurando entorno de testing..."
cp .env.example .env.testing

# Generar clave de aplicación
php artisan key:generate --env=testing

# Crear directorios necesarios
mkdir -p bootstrap/cache
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
chmod -R 775 storage bootstrap/cache

# Ejecutar migraciones
log "🗄️ Ejecutando migraciones..."
php artisan migrate:all --seed --env=testing

# Ejecutar tests
log "🧪 Ejecutando tests..."
php artisan test --coverage --min=80

# Verificar estilo de código
log "🎨 Verificando estilo de código..."
if command -v ./vendor/bin/pint &> /dev/null; then
    ./vendor/bin/pint --test
else
    warning "Laravel Pint no está instalado. Instalando..."
    composer require --dev laravel/pint
    ./vendor/bin/pint --test
fi

# Build assets
log "🔨 Construyendo assets..."
npm run build

success "🎉 Todas las pruebas locales pasaron exitosamente!"
log "✅ El código está listo para hacer push al repositorio." 