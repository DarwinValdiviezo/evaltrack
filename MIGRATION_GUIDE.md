# Guía de Migración: EvalTrack v1.0.0 → v2.0.0

## 📋 Resumen de la Migración

**EvalTrack** ha sido completamente migrado desde **Laravel 12.0** a una arquitectura moderna con **NestJS** y **React**.

### 🔄 Cambios Principales

| Aspecto | v1.0.0 (Laravel) | v2.0.0 (NestJS/React) |
|---------|------------------|----------------------|
| **Backend** | Laravel 12.0 (PHP) | NestJS 11.0 (TypeScript) |
| **Frontend** | Blade + Bootstrap | React + Tailwind CSS |
| **Base de Datos** | PostgreSQL + MySQL | PostgreSQL (unificada) |
| **ORM** | Eloquent | Prisma |
| **Autenticación** | Laravel UI | JWT + Passport |
| **Arquitectura** | Monolítica | API + SPA |
| **Performance** | Server-side rendering | Client-side rendering |

## 🏗️ Estructura del Repositorio

```
evaltrack/
├── master/                    # Rama principal (v1.0.0 Laravel)
├── develop/                   # Rama de desarrollo (v1.0.0)
├── v2.0.0-nestjs-react/       # Nueva versión (v2.0.0)
│   ├── backend/              # API NestJS
│   ├── frontend/             # Aplicación React
│   ├── scripts/              # Scripts de despliegue
│   └── plan_despliegue_produccion.md
└── tags/
    ├── v1.0.0               # Tag de Laravel
    └── v2.0.0               # Tag de NestJS/React
```

## 🚀 Cómo Usar la Nueva Versión

### Opción 1: Usar la Rama v2.0.0-nestjs-react

```bash
# Clonar el repositorio
git clone https://github.com/DarwinValdiviezo/evaltrack.git
cd evaltrack

# Cambiar a la versión v2.0.0
git checkout v2.0.0-nestjs-react

# Instalar dependencias del backend
cd backend
npm install
cp .env.example .env
# Configurar variables de entorno

# Instalar dependencias del frontend
cd ../frontend
npm install
cp .env.example .env
# Configurar variables de entorno
```

### Opción 2: Usar el Tag v2.0.0

```bash
# Clonar y cambiar al tag específico
git clone https://github.com/DarwinValdiviezo/evaltrack.git
cd evaltrack
git checkout v2.0.0
```

## 📊 Migración de Datos

### Script de Migración

Para migrar datos desde la versión Laravel v1.0.0:

```bash
# Ejecutar script de migración
./scripts/migrate-data.sh

# Verificar migración
./scripts/verify-migration.sh
```

### Datos Migrados

- ✅ Usuarios y roles
- ✅ Empleados y perfiles
- ✅ Eventos y estados
- ✅ Asistencias y confirmaciones
- ✅ Evaluaciones y calificaciones

## 🔧 Configuración

### Variables de Entorno

**Backend (.env):**
```env
DATABASE_URL="postgresql://user:password@localhost:5432/evaltrack_v2"
JWT_SECRET="tu-jwt-secret-super-seguro"
JWT_EXPIRES_IN="24h"
PORT=3000
```

**Frontend (.env):**
```env
VITE_API_URL="http://localhost:3000"
VITE_APP_NAME="EvalTrack v2.0"
```

## 🚀 Despliegue

### Desarrollo Local

```bash
# Backend
cd backend && npm run start:dev

# Frontend
cd frontend && npm run dev
```

### Docker

```bash
docker-compose up -d
```

### Producción

Ver [Plan de Despliegue de Producción](./plan_despliegue_produccion.md) para instrucciones completas.

## 📚 Documentación

- [Plan de Despliegue](./plan_despliegue_produccion.md)
- [API Documentation](./backend/API_ENDPOINTS.md)
- [README Principal](./README.md)

## 🔄 Rollback

Si necesitas volver a la versión Laravel v1.0.0:

```bash
git checkout master
# o
git checkout v1.0.0
```

## 📞 Soporte

- **Issues**: [GitHub Issues](https://github.com/DarwinValdiviezo/evaltrack/issues)
- **Documentación**: [Wiki del proyecto](https://github.com/DarwinValdiviezo/evaltrack/wiki)

---

**EvalTrack v2.0.0** - Migración exitosa a NestJS + React 🎉 