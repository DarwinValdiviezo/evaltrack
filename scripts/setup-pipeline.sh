#!/bin/bash

# Script para configurar el pipeline CI/CD de EvalTrack
# Uso: ./scripts/setup-pipeline.sh

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

log "🚀 Configurando pipeline CI/CD para EvalTrack..."

# Verificar que estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    error "No se encontró el archivo artisan. Ejecuta este script desde la raíz del proyecto."
fi

# Crear estructura de carpetas para GitHub Actions
log "📁 Creando estructura de carpetas..."
mkdir -p .github/workflows
success "Estructura de carpetas creada"

# Verificar si los archivos de workflow ya existen
if [ -f ".github/workflows/ci-cd.yml" ]; then
    warning "El archivo .github/workflows/ci-cd.yml ya existe"
    read -p "¿Quieres sobrescribirlo? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        log "Saltando creación de ci-cd.yml"
    fi
fi

if [ -f ".github/workflows/ci-simple.yml" ]; then
    warning "El archivo .github/workflows/ci-simple.yml ya existe"
    read -p "¿Quieres sobrescribirlo? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        log "Saltando creación de ci-simple.yml"
    fi
fi

# Verificar que Docker esté instalado
log "🐳 Verificando Docker..."
if command -v docker &> /dev/null; then
    success "Docker está instalado: $(docker --version)"
else
    warning "Docker no está instalado. El pipeline funcionará pero no podrás hacer build local."
fi

# Verificar que git esté configurado
log "📝 Verificando configuración de Git..."
if git config --get user.name &> /dev/null; then
    success "Git configurado: $(git config --get user.name)"
else
    warning "Git no está configurado. Configúralo con:"
    echo "  git config --global user.name 'Tu Nombre'"
    echo "  git config --global user.email 'tu@email.com'"
fi

# Mostrar información sobre el repositorio
log "📋 Información del repositorio:"
if git remote -v | grep origin &> /dev/null; then
    echo "  Repositorio remoto: $(git remote get-url origin)"
else
    warning "No hay repositorio remoto configurado"
fi

# Mostrar rama actual
echo "  Rama actual: $(git branch --show-current)"

# Instrucciones para configurar Docker Hub
log "🔧 Configuración de Docker Hub:"
echo ""
echo "1. Ve a https://hub.docker.com/"
echo "2. Crea una cuenta o inicia sesión"
echo "3. Ve a Account Settings → Security"
echo "4. Crea un Access Token con permisos 'Read & Write'"
echo "5. Copia el token"
echo ""

# Instrucciones para configurar GitHub Secrets
log "🔐 Configuración de GitHub Secrets:"
echo ""
echo "1. Ve a tu repositorio: https://github.com/DarwinValdiviezo/evaltrack"
echo "2. Haz clic en Settings → Secrets and variables → Actions"
echo "3. Agrega los siguientes secrets:"
echo ""
echo "   DOCKER_USERNAME:"
echo "   - Name: DOCKER_USERNAME"
echo "   - Value: tu_username_de_docker_hub"
echo ""
echo "   DOCKER_PASSWORD:"
echo "   - Name: DOCKER_PASSWORD"
echo "   - Value: tu_access_token_de_docker_hub"
echo ""

# Opciones de pipeline
log "🎯 Opciones de pipeline disponibles:"
echo ""
echo "1. Pipeline Simple (ci-simple.yml):"
echo "   ✅ Tests automáticos"
echo "   ✅ Build de Docker (sin push)"
echo "   ✅ Análisis de código"
echo "   ❌ No requiere Docker Hub"
echo ""
echo "2. Pipeline Completo (ci-cd.yml):"
echo "   ✅ Tests automáticos"
echo "   ✅ Build y push a Docker Hub"
echo "   ✅ Despliegue automático"
echo "   ❌ Requiere Docker Hub configurado"
echo ""

read -p "¿Qué pipeline quieres usar? (1=Simple, 2=Completo): " -n 1 -r
echo

if [[ $REPLY =~ ^[1]$ ]]; then
    log "🎯 Usando pipeline simple..."
    if [ -f ".github/workflows/ci-simple.yml" ]; then
        success "Pipeline simple ya está configurado"
    else
        error "El archivo ci-simple.yml no existe. Asegúrate de que se haya creado correctamente."
    fi
elif [[ $REPLY =~ ^[2]$ ]]; then
    log "🎯 Usando pipeline completo..."
    if [ -f ".github/workflows/ci-cd.yml" ]; then
        success "Pipeline completo ya está configurado"
    else
        error "El archivo ci-cd.yml no existe. Asegúrate de que se haya creado correctamente."
    fi
    
    warning "Recuerda configurar Docker Hub antes de hacer push"
else
    error "Opción inválida. Usando pipeline simple por defecto."
fi

# Comandos para hacer commit y push
log "📤 Comandos para activar el pipeline:"
echo ""
echo "git add .github/workflows/"
echo "git commit -m 'Agregar pipeline CI/CD'"
echo "git push origin main"
echo ""

# Verificar archivos creados
log "📋 Archivos creados:"
if [ -f ".github/workflows/ci-simple.yml" ]; then
    echo "  ✅ .github/workflows/ci-simple.yml"
fi
if [ -f ".github/workflows/ci-cd.yml" ]; then
    echo "  ✅ .github/workflows/ci-cd.yml"
fi
if [ -f "DOCKER_SETUP.md" ]; then
    echo "  ✅ DOCKER_SETUP.md"
fi

# Probar build local si Docker está disponible
if command -v docker &> /dev/null; then
    log "🧪 Probando build local..."
    read -p "¿Quieres probar el build local de Docker? (y/N): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        log "Construyendo imagen Docker..."
        if docker build -t evaltrack:test .; then
            success "Build local exitoso"
            echo "  Imagen: evaltrack:test"
            echo "  Tamaño: $(docker images evaltrack:test --format '{{.Size}}')"
        else
            error "Build local falló"
        fi
    fi
fi

success "🎉 Configuración del pipeline completada!"
echo ""
echo "📝 Próximos pasos:"
echo "1. Configura Docker Hub (si usas pipeline completo)"
echo "2. Agrega los secrets en GitHub"
echo "3. Haz commit y push del código"
echo "4. Ve a la pestaña Actions en GitHub para ver el pipeline"
echo ""
echo "📚 Documentación adicional:"
echo "- DOCKER_SETUP.md: Guía completa de Docker Hub"
echo "- DOCUMENTO_DESPLIEGUE.md: Documento completo de DevOps"
echo "" 