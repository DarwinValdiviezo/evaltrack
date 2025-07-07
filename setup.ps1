Write-Host "🚀 Configurando Sistema de Gestión de Talento Humano" -ForegroundColor Green
Write-Host "==================================================" -ForegroundColor Green

# Verificar si Node.js está instalado
try {
    $nodeVersion = node --version
    Write-Host "✅ Node.js encontrado: $nodeVersion" -ForegroundColor Green
} catch {
    Write-Host "❌ Node.js no está instalado. Por favor instala Node.js 18+ primero." -ForegroundColor Red
    exit 1
}

# Configurar backend
Write-Host ""
Write-Host "📦 Configurando Backend..." -ForegroundColor Yellow
Set-Location backend

# Instalar dependencias
Write-Host "Instalando dependencias del backend..." -ForegroundColor Cyan
npm install

# Verificar si existe archivo .env
if (-not (Test-Path ".env")) {
    Write-Host "📝 Creando archivo .env para el backend..." -ForegroundColor Cyan
    @"
DATABASE_URL="postgresql://postgres:password@localhost:5432/gestion_talento"
JWT_SECRET="tu-secreto-jwt-super-seguro-cambiar-en-produccion"
PORT=3000
"@ | Out-File -FilePath ".env" -Encoding UTF8
    Write-Host "⚠️  Por favor edita el archivo backend/.env con tus credenciales de PostgreSQL" -ForegroundColor Yellow
}

# Generar cliente Prisma
Write-Host "🔧 Generando cliente de Prisma..." -ForegroundColor Cyan
npm run db:generate

# Ejecutar migraciones
Write-Host "🗄️  Ejecutando migraciones de la base de datos..." -ForegroundColor Cyan
npm run db:migrate

# Poblar base de datos
Write-Host "🌱 Poblando base de datos con datos de prueba..." -ForegroundColor Cyan
npm run db:seed

Set-Location ..

# Configurar frontend
Write-Host ""
Write-Host "📦 Configurando Frontend..." -ForegroundColor Yellow
Set-Location frontend

# Instalar dependencias
Write-Host "Instalando dependencias del frontend..." -ForegroundColor Cyan
npm install

# Crear archivo .env para frontend
if (-not (Test-Path ".env")) {
    Write-Host "📝 Creando archivo .env para el frontend..." -ForegroundColor Cyan
    "VITE_API_URL=http://localhost:3000" | Out-File -FilePath ".env" -Encoding UTF8
}

Set-Location ..

Write-Host ""
Write-Host "✅ Configuración completada!" -ForegroundColor Green
Write-Host ""
Write-Host "🎯 Próximos pasos:" -ForegroundColor Yellow
Write-Host "1. Edita backend/.env con tus credenciales de PostgreSQL" -ForegroundColor White
Write-Host "2. Inicia el backend: cd backend && npm run start:dev" -ForegroundColor White
Write-Host "3. Inicia el frontend: cd frontend && npm run dev" -ForegroundColor White
Write-Host ""
Write-Host "👥 Credenciales de prueba:" -ForegroundColor Yellow
Write-Host "   Admin: admin@empresa.com / admin123" -ForegroundColor White
Write-Host "   RRHH: hr@empresa.com / hr123" -ForegroundColor White
Write-Host "   Empleado: empleado1@empresa.com / empleado123" -ForegroundColor White
Write-Host ""
Write-Host "🌐 URLs:" -ForegroundColor Yellow
Write-Host "   Frontend: http://localhost:5173" -ForegroundColor White
Write-Host "   Backend: http://localhost:3000" -ForegroundColor White
Write-Host "   Prisma Studio: http://localhost:5555 (npm run db:studio)" -ForegroundColor White 