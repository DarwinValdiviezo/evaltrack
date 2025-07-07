#!/bin/bash

# Script ultra-rápido para levantar EvalTrack con Docker
# Uso: ./scripts/docker-quick.sh

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

log "🚀 Levantando EvalTrack con Docker (modo rápido)..."

# Verificar Docker
if ! command -v docker &> /dev/null; then
    error "Docker no está instalado"
fi

if ! command -v docker-compose &> /dev/null; then
    error "Docker Compose no está instalado"
fi

# Detener contenedores existentes
log "🛑 Deteniendo contenedores existentes..."
docker-compose down --remove-orphans 2>/dev/null || true

# Limpiar cache de Docker
log "🧹 Limpiando cache..."
docker system prune -f

# Construir y levantar
log "🔨 Construyendo y levantando servicios..."
docker-compose up -d --build

# Esperar a que esté listo
log "⏳ Esperando a que los servicios estén listos..."
sleep 30

# Ejecutar migraciones
log "🗄️ Ejecutando migraciones..."
docker-compose exec -T app php artisan migrate --database=pgsql --path=database/migrations/users --force || warning "Error en migración PostgreSQL"
docker-compose exec -T app php artisan migrate --database=mysql_business --path=database/migrations/business --force || warning "Error en migración MySQL"

# Ejecutar seeders
log "🌱 Ejecutando seeders..."
docker-compose exec -T app php artisan db:seed --database=mysql_business --force || warning "Error en seeders"

# Optimizar
log "⚡ Optimizando aplicación..."
docker-compose exec -T app php artisan config:cache
docker-compose exec -T app php artisan route:cache
docker-compose exec -T app php artisan view:cache

success "🎉 ¡EvalTrack está listo!"

echo ""
echo "🌐 Acceso a la aplicación:"
echo "   ✅ Aplicación: http://localhost:8000"
echo "   🏥 Health Check: http://localhost:8000/health"
echo "   📧 MailHog: http://localhost:8025"
echo "   🗄️ Adminer: http://localhost:8080"
echo ""
echo "👤 Credenciales:"
echo "   - Usuario: admin@evaltrack.com"
echo "   - Contraseña: password"
echo ""
echo "🔧 Comandos útiles:"
echo "   - Ver logs: docker-compose logs -f app"
echo "   - Reiniciar: docker-compose restart app"
echo "   - Detener: docker-compose down"
echo "" 