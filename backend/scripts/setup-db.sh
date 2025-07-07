#!/bin/bash

# ========================================
# SCRIPT DE CONFIGURACIÓN DE BASE DE DATOS
# ========================================

echo "🚀 Configurando base de datos para Sistema de Talento Humano..."

# Verificar si PostgreSQL está instalado
if ! command -v psql &> /dev/null; then
    echo "❌ PostgreSQL no está instalado. Por favor instálalo primero."
    exit 1
fi

# Crear base de datos si no existe
echo "📊 Creando base de datos..."
createdb -U postgres talento_humano_dev 2>/dev/null || echo "Base de datos ya existe"

# Ejecutar migraciones
echo "🔄 Ejecutando migraciones..."
npx prisma migrate dev --name init

# Generar cliente Prisma
echo "🔧 Generando cliente Prisma..."
npx prisma generate

# Crear usuario administrador por defecto
echo "👤 Creando usuario administrador por defecto..."
npx prisma db seed

echo "✅ Configuración completada!"
echo ""
echo "📋 Próximos pasos:"
echo "1. Copia .env.example a .env y configura las variables"
echo "2. Ejecuta: npm run start:dev"
echo "3. Ve a http://localhost:3000"
echo ""
echo "🔑 Credenciales por defecto:"
echo "Email: admin@empresa.com"
echo "Password: admin123" 