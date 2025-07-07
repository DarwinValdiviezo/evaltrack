# EvalTrack v2.0.0 - Sistema de Gestión de Talento Humano

[![NestJS](https://img.shields.io/badge/NestJS-v11.0.1-red.svg)](https://nestjs.com/)
[![React](https://img.shields.io/badge/React-v18-blue.svg)](https://reactjs.org/)
[![TypeScript](https://img.shields.io/badge/TypeScript-v5.7.3-blue.svg)](https://www.typescriptlang.org/)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-v15-green.svg)](https://www.postgresql.org/)

## 📋 Descripción

**EvalTrack v2.0.0** es la nueva versión del Sistema de Gestión de Talento Humano, migrado desde Laravel 12.0 a una arquitectura moderna con **NestJS** (Backend) y **React** (Frontend). Esta versión mantiene toda la funcionalidad de la v1.0.0 pero con mejoras significativas en performance, escalabilidad y experiencia de usuario.

### 🚀 Evolución del Sistema

| Versión | Tecnologías | Estado |
|---------|-------------|--------|
| **v1.0.0** | Laravel 12.0 + PHP + Bootstrap | ✅ Legacy (Migrado) |
| **v2.0.0** | NestJS + React + TypeScript | 🆕 Actual |

**Repositorio v1.0.0**: [https://github.com/DarwinValdiviezo/evaltrack.git](https://github.com/DarwinValdiviezo/evaltrack.git)

### ✨ Características Principales

* **Gestión de Usuarios y Roles**: Sistema de permisos granular con JWT
* **Gestión de Empleados**: CRUD completo con perfiles detallados
* **Gestión de Eventos**: Creación, edición y seguimiento de eventos corporativos
* **Control de Asistencias**: Registro y confirmación de asistencia a eventos
* **Sistema de Evaluaciones**: Evaluaciones post-evento con calificaciones
* **Dashboard Personalizado**: Interfaz adaptada según el rol del usuario
* **Arquitectura Moderna**: API RESTful + SPA con TypeScript

## 🏗️ Arquitectura

### Tecnologías Utilizadas

* **Backend**: NestJS 11.0.1 (TypeScript)
* **Frontend**: React 18 + Vite + TypeScript
* **Base de Datos**: PostgreSQL 15 (unificada desde PostgreSQL + MySQL)
* **ORM**: Prisma 6.11.1
* **Autenticación**: JWT + Passport
* **UI Framework**: Tailwind CSS
* **Contenedores**: Docker + Docker Compose
* **CI/CD**: GitHub Actions

### Estructura del Proyecto

```
evaltrack-v2/
├── backend/                 # API NestJS
│   ├── src/
│   │   ├── auth/           # Autenticación JWT
│   │   ├── users/          # Gestión de usuarios
│   │   ├── events/         # Gestión de eventos
│   │   ├── attendances/    # Control de asistencias
│   │   ├── evaluations/    # Sistema de evaluaciones
│   │   └── prisma/         # Configuración de BD
│   ├── package.json
│   └── Dockerfile
├── frontend/               # Aplicación React
│   ├── src/
│   │   ├── components/     # Componentes reutilizables
│   │   ├── pages/          # Páginas de la aplicación
│   │   ├── contexts/       # Contextos de React
│   │   └── types/          # Tipos TypeScript
│   ├── package.json
│   └── Dockerfile
├── scripts/                # Scripts de despliegue
├── docker-compose.yml      # Configuración Docker
└── README.md
```

## 🚀 Instalación

### Prerrequisitos

* Node.js 18+ y npm
* PostgreSQL 15+
* Docker y Docker Compose (opcional)

### Instalación Local

1. **Clonar el repositorio**
```bash
git clone https://github.com/DarwinValdiviezo/evaltrack.git
cd evaltrack
git checkout v2.0.0
```

2. **Configurar Backend**
```bash
cd backend
npm install
cp .env.example .env
# Configurar variables de entorno en .env
npm run db:generate
npm run db:migrate
npm run db:seed
npm run start:dev
```

3. **Configurar Frontend**
```bash
cd frontend
npm install
cp .env.example .env
# Configurar variables de entorno en .env
npm run dev
```

### Instalación con Docker

```bash
# Clonar y configurar
git clone https://github.com/DarwinValdiviezo/evaltrack.git
cd evaltrack
git checkout v2.0.0

# Iniciar servicios
docker-compose up -d

# Configurar base de datos
docker-compose exec backend npm run db:migrate
docker-compose exec backend npm run db:seed
```

## 👥 Roles y Permisos

### Administrador
* Gestión completa de usuarios y roles
* Creación y eliminación de eventos
* Acceso a todas las funcionalidades
* Dashboard con estadísticas completas

### Gestor de Talento Humano
* Gestión de empleados (CRUD)
* Gestión de eventos (edición)
* Gestión de asistencias
* Gestión y calificación de evaluaciones

### Empleado
* Visualización de su perfil
* Gestión de sus asistencias
* Confirmación de asistencia a eventos
* Respuesta a evaluaciones

## 📊 Funcionalidades

### Dashboard Personalizado
* **Administrador**: Estadísticas de usuarios, roles, empleados, eventos
* **Gestor**: Próximos eventos, asistencias pendientes, evaluaciones por calificar
* **Empleado**: Asistencias confirmadas/pendientes, evaluaciones, promedio de calificaciones

### Gestión de Eventos
* Creación de eventos con tipos (Capacitación, Reunión, Taller, etc.)
* Estados de eventos (Programado, En Curso, Completado, Cancelado)
* Asignación automática de asistencias

### Control de Asistencias
* Registro automático de asistencias al crear eventos
* Confirmación de asistencia por parte de empleados
* Estados de asistencia (Registrada, Confirmada)
* Creación automática de evaluaciones

### Sistema de Evaluaciones
* Evaluaciones automáticas tras confirmar asistencia
* Preguntas personalizables
* Estados de evaluación (Pendiente, Disponible, Completada, Calificada)
* Sistema de calificación

## 🔧 Configuración

### Variables de Entorno Importantes

```env
# Backend (.env)
DATABASE_URL="postgresql://user:password@localhost:5432/evaltrack_v2"
JWT_SECRET="tu-jwt-secret-super-seguro"
JWT_EXPIRES_IN="24h"
PORT=3000

# Frontend (.env)
VITE_API_URL="http://localhost:3000"
VITE_APP_NAME="EvalTrack v2.0"
```

### Comandos Útiles

```bash
# Backend
npm run start:dev          # Desarrollo
npm run build             # Build para producción
npm run start:prod        # Producción
npm run test              # Tests
npm run db:migrate        # Migraciones
npm run db:seed           # Datos de prueba

# Frontend
npm run dev               # Desarrollo
npm run build             # Build para producción
npm run preview           # Preview de producción
npm run test              # Tests
```

## 🧪 Testing

```bash
# Backend tests
cd backend
npm run test
npm run test:e2e

# Frontend tests
cd frontend
npm run test
```

## 📚 Documentación

* [Plan de Despliegue de Producción](./plan_despliegue_produccion.md)
* [Guía de Migración desde v1.0.0](./docs/migration-guide.md)
* [API Documentation](./docs/api.md)

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

## 🔄 Migración desde v1.0.0

Para migrar datos desde la versión Laravel v1.0.0:

```bash
# Ejecutar script de migración
./scripts/migrate-data.sh

# Verificar migración
./scripts/verify-migration.sh
```

## 🤝 Contribución

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT - ver el archivo LICENSE para detalles.

## 👨‍💻 Equipo

* **Desarrollador Principal**: Darwin Valdiviezo
* **DevOps Engineer**: [Nombre]
* **UI/UX Designer**: [Nombre]

## 📞 Soporte

* **Email**: soporte@evaltrack.com
* **Documentación**: Wiki del proyecto
* **Issues**: GitHub Issues

---

**EvalTrack v2.0.0** - Sistema de Gestión de Talento Humano (NestJS + React)

*Migrado desde Laravel v1.0.0 con mejoras significativas en performance y escalabilidad.* 