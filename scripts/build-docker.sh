#!/bin/bash
set -e

echo "🐳 Construyendo imágenes Docker para EvalTrack v2.0.0..."

# Variables
BACKEND_IMAGE="darwinvaldiviezo/evaltrack-api:latest"
FRONTEND_IMAGE="darwinvaldiviezo/evaltrack-frontend:latest"
VERSION=${1:-latest}

echo "📦 Versión: $VERSION"

# ========================================
# BACKEND IMAGE
# ========================================
echo "🔧 Construyendo imagen del backend..."
cd backend
docker build -t $BACKEND_IMAGE:$VERSION .
echo "✅ Backend image construida: $BACKEND_IMAGE:$VERSION"

# ========================================
# FRONTEND IMAGE
# ========================================
echo "🎨 Construyendo imagen del frontend..."
cd ../frontend
docker build -t $FRONTEND_IMAGE:$VERSION .
echo "✅ Frontend image construida: $FRONTEND_IMAGE:$VERSION"

# ========================================
# VERIFICAR IMÁGENES
# ========================================
echo "🔍 Verificando imágenes construidas..."
docker images | grep darwinvaldiviezo

# ========================================
# TEST LOCAL (OPCIONAL)
# ========================================
if [ "$2" = "--test" ]; then
    echo "🧪 Ejecutando test local..."
    cd ..
    
    # Iniciar servicios básicos
    docker-compose up -d postgres redis
    
    # Esperar a que estén listos
    echo "⏳ Esperando a que los servicios estén listos..."
    sleep 10
    
    # Test backend
    echo "🔧 Probando backend..."
    docker run --rm --network evaltrack_evaltrack-network \
      -e DATABASE_URL="postgresql://evaltrack_user:password123@postgres:5432/evaltrack_dev" \
      -e JWT_SECRET="test-secret" \
      $BACKEND_IMAGE:$VERSION npm run test
    
    # Test frontend
    echo "🎨 Probando frontend..."
    docker run --rm $FRONTEND_IMAGE:$VERSION npm run test
    
    echo "✅ Tests locales completados"
fi

echo "🎉 ¡Construcción de imágenes completada!"
echo ""
echo "📋 Comandos útiles:"
echo "  docker run -p 3000:3000 $BACKEND_IMAGE:$VERSION"
echo "  docker run -p 5173:80 $FRONTEND_IMAGE:$VERSION"
echo "  docker-compose up -d"
echo ""
echo "🚀 Para subir a Docker Hub:"
echo "  docker push $BACKEND_IMAGE:$VERSION"
echo "  docker push $FRONTEND_IMAGE:$VERSION" 