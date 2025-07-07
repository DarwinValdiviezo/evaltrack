#!/bin/bash

echo "🚀 Configurando Sistema de Gestión de Talento Humano"
echo "=================================================="

# Verificar si Node.js está instalado
if ! command -v node &> /dev/null; then
    echo "❌ Node.js no está instalado. Por favor instala Node.js 18+ primero."
    exit 1
fi

# Verificar si PostgreSQL está instalado
if ! command -v psql &> /dev/null; then
    echo "⚠️  PostgreSQL no está instalado. Asegúrate de tener PostgreSQL configurado."
fi

echo "✅ Node.js encontrado: $(node --version)"

# Configurar backend
echo ""
echo "📦 Configurando Backend..."
cd backend

# Instalar dependencias
echo "Instalando dependencias del backend..."
npm install

# Verificar si existe archivo .env
if [ ! -f .env ]; then
    echo "📝 Creando archivo .env para el backend..."
    cat > .env << EOF
DATABASE_URL="postgresql://postgres:password@localhost:5432/gestion_talento"
JWT_SECRET="tu-secreto-jwt-super-seguro-cambiar-en-produccion"
PORT=3000
EOF
    echo "⚠️  Por favor edita el archivo backend/.env con tus credenciales de PostgreSQL"
fi

# Generar cliente Prisma
echo "🔧 Generando cliente de Prisma..."
npm run db:generate

# Ejecutar migraciones
echo "🗄️  Ejecutando migraciones de la base de datos..."
npm run db:migrate

# Poblar base de datos
echo "🌱 Poblando base de datos con datos de prueba..."
npm run db:seed

cd ..

# Configurar frontend
echo ""
echo "📦 Configurando Frontend..."
cd frontend

# Instalar dependencias
echo "Instalando dependencias del frontend..."
npm install

# Crear archivo .env para frontend
if [ ! -f .env ]; then
    echo "📝 Creando archivo .env para el frontend..."
    echo "VITE_API_URL=http://localhost:3000" > .env
fi

cd ..

echo ""
echo "✅ Configuración completada!"
echo ""
echo "🎯 Próximos pasos:"
echo "1. Edita backend/.env con tus credenciales de PostgreSQL"
echo "2. Inicia el backend: cd backend && npm run start:dev"
echo "3. Inicia el frontend: cd frontend && npm run dev"
echo ""
echo "👥 Credenciales de prueba:"
echo "   Admin: admin@empresa.com / admin123"
echo "   RRHH: hr@empresa.com / hr123"
echo "   Empleado: empleado1@empresa.com / empleado123"
echo ""
echo "🌐 URLs:"
echo "   Frontend: http://localhost:5173"
echo "   Backend: http://localhost:3000"
echo "   Prisma Studio: http://localhost:5555 (npm run db:studio)" 