# 🏢 Sistema de Gestión de Talento Humano

Sistema completo para la gestión de eventos, asistencias y evaluaciones de capacitación empresarial.

## 🎯 Características

### 👥 Roles del Sistema
- **Administrador**: Control total del sistema
- **Recursos Humanos**: Gestión de asistencias y evaluaciones
- **Empleado**: Ver eventos, marcar asistencia, realizar evaluaciones

### 🚀 Funcionalidades

#### Administrador
- ✅ Gestión completa de usuarios y roles
- ✅ Crear, editar y gestionar eventos de capacitación
- ✅ Activar/desactivar eventos
- ✅ Control total de asistencias y evaluaciones

#### Recursos Humanos
- ✅ Ver eventos (solo lectura)
- ✅ Control de asistencias
- ✅ Editar fechas/horas de asistencias
- ✅ Calificar evaluaciones de empleados
- ✅ Reportes de asistencia y desempeño

#### Empleado
- ✅ Ver eventos activos
- ✅ Marcar asistencia a eventos
- ✅ Realizar evaluaciones post-evento
- ✅ Ver reportes personales

## 🛠️ Tecnologías

- **Backend**: NestJS + TypeScript
- **Base de Datos**: PostgreSQL + Prisma ORM
- **Autenticación**: JWT + Passport
- **Validación**: class-validator
- **Documentación**: Swagger/OpenAPI

## 📋 Requisitos

- Node.js 18+
- PostgreSQL 15+
- npm o yarn

## 🚀 Instalación

### 1. Clonar el repositorio
```bash
git clone <tu-repositorio>
cd backend
```

### 2. Instalar dependencias
```bash
npm install
```

### 3. Configurar variables de entorno
```bash
# Crear archivo .env basado en .env.example
cp .env.example .env

# Editar .env con tus configuraciones
DATABASE_URL="postgresql://usuario:contraseña@localhost:5432/talento_humano_dev"
JWT_SECRET="tu_jwt_secret_super_seguro"
```

### 4. Configurar base de datos
```bash
# Generar cliente Prisma
npm run db:generate

# Ejecutar migraciones
npm run db:migrate

# Crear datos iniciales
npm run db:seed
```

### 5. Ejecutar el servidor
```bash
# Desarrollo
npm run start:dev

# Producción
npm run build
npm run start:prod
```

## 🔑 Credenciales por Defecto

Después de ejecutar el seed, tendrás estos usuarios:

| Rol | Email | Password |
|-----|-------|----------|
| Administrador | admin@empresa.com | admin123 |
| Recursos Humanos | hr@empresa.com | hr123 |
| Empleado | empleado1@empresa.com | empleado123 |

## 📚 API Endpoints

### Autenticación
```
POST /auth/login - Iniciar sesión
```

### Usuarios
```
GET    /users - Obtener todos los usuarios
GET    /users/:id - Obtener usuario específico
POST   /users - Crear usuario (Admin)
PATCH  /users/:id - Actualizar usuario (Admin)
DELETE /users/:id - Eliminar usuario (Admin)
```

### Eventos
```
GET    /events - Obtener eventos
GET    /events/:id - Obtener evento específico
POST   /events - Crear evento (Admin)
PATCH  /events/:id - Actualizar evento (Admin)
PATCH  /events/:id/toggle-status - Activar/desactivar evento (Admin)
DELETE /events/:id - Eliminar evento (Admin)
```

### Asistencias
```
GET    /attendances - Obtener asistencias
GET    /attendances/:id - Obtener asistencia específica
POST   /attendances - Marcar asistencia
PATCH  /attendances/:id - Actualizar asistencia (RRHH)
DELETE /attendances/:id - Eliminar asistencia (Admin)
```

### Evaluaciones
```
GET    /evaluations - Obtener evaluaciones
GET    /evaluations/:id - Obtener evaluación específica
POST   /evaluations - Crear evaluación
PATCH  /evaluations/:id - Calificar evaluación (RRHH)
DELETE /evaluations/:id - Eliminar evaluación (Admin)
```

## 🔒 Autenticación y Autorización

### JWT Token
Incluye el token JWT en el header de las peticiones:
```
Authorization: Bearer <tu_token_jwt>
```

### Roles y Permisos
- **ADMIN**: Acceso total al sistema
- **RECURSOS_HUMANOS**: Gestión de asistencias y evaluaciones
- **EMPLEADO**: Ver eventos y gestionar sus propias asistencias

## 🗄️ Estructura de Base de Datos

### Modelos Principales
- **User**: Usuarios del sistema con roles
- **Event**: Eventos de capacitación
- **Attendance**: Asistencias a eventos
- **Evaluation**: Evaluaciones post-evento

### Relaciones
- Un usuario puede crear múltiples eventos
- Un evento puede tener múltiples asistencias
- Una asistencia puede tener una evaluación
- Un usuario puede calificar múltiples evaluaciones

## 🧪 Testing

```bash
# Tests unitarios
npm run test

# Tests e2e
npm run test:e2e

# Coverage
npm run test:cov
```

## 📊 Monitoreo y Logs

El sistema incluye:
- Logs estructurados
- Health checks
- Métricas de rendimiento
- Manejo de errores centralizado

## 🚀 Despliegue

### Docker
```bash
# Construir imagen
docker build -t talento-humano-api .

# Ejecutar contenedor
docker run -p 3000:3000 --env-file .env talento-humano-api
```

### Docker Compose
```bash
# Ejecutar todo el stack
docker-compose -f docker-compose.prod.yml up -d
```

## 📝 Scripts Útiles

```bash
# Configuración completa de BD
npm run db:setup

# Ver base de datos en Prisma Studio
npm run db:studio

# Linting y formateo
npm run lint
npm run format

# Build para producción
npm run build
```

## 🤝 Contribución

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT - ver el archivo [LICENSE](LICENSE) para detalles.

## 🆘 Soporte

Si tienes problemas o preguntas:
- Abre un issue en GitHub
- Contacta al equipo de desarrollo
- Revisa la documentación de NestJS y Prisma

---

**Desarrollado con ❤️ para la gestión eficiente del talento humano**
