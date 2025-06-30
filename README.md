# EvalTrack - Sistema de Gestión de Talento Humano

[![Laravel](https://img.shields.io/badge/Laravel-12.0-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

## 📋 Descripción

EvalTrack es un sistema integral de gestión de talento humano desarrollado en Laravel 12.0 que permite la administración completa de empleados, eventos, asistencias y evaluaciones corporativas.

### ✨ Características Principales

- **Gestión de Usuarios y Roles**: Sistema de permisos granular con Spatie Laravel Permission
- **Gestión de Empleados**: CRUD completo con perfiles detallados
- **Gestión de Eventos**: Creación, edición y seguimiento de eventos corporativos
- **Control de Asistencias**: Registro y confirmación de asistencia a eventos
- **Sistema de Evaluaciones**: Evaluaciones post-evento con calificaciones
- **Dashboard Personalizado**: Interfaz adaptada según el rol del usuario
- **Arquitectura Híbrida**: PostgreSQL para usuarios/roles, MySQL para datos de negocio

## 🏗️ Arquitectura

### Tecnologías Utilizadas

- **Backend**: Laravel 12.0 (PHP 8.2+)
- **Frontend**: Bootstrap 4.6.2 + SB Admin 2
- **Base de Datos**: PostgreSQL 15 + MySQL 8.0
- **Cache**: Redis 7.0
- **Autenticación**: Laravel UI + Spatie Permission
- **Contenedores**: Docker + Docker Compose
- **CI/CD**: GitHub Actions
- **Orquestación**: Kubernetes (producción)

### Estructura de Base de Datos

```
PostgreSQL (Usuarios y Roles)
├── users
├── roles
├── permissions
└── model_has_roles

MySQL (Datos de Negocio)
├── employees
├── eventos
├── asistencias
└── evaluaciones
```

## 🚀 Instalación

### Prerrequisitos

- PHP 8.2 o superior
- Composer 2.0 o superior
- Node.js 18+ y npm
- PostgreSQL 15+
- MySQL 8.0+
- Redis 7.0+ (opcional)

### Instalación Local

1. **Clonar el repositorio**
```bash
git clone https://github.com/tu-usuario/evaltrack.git
cd evaltrack
```

2. **Instalar dependencias PHP**
```bash
composer install
```

3. **Instalar dependencias Node.js**
```bash
npm install
```

4. **Configurar variables de entorno**
```bash
cp env.example .env
php artisan key:generate
```

5. **Configurar bases de datos**
```bash
# Crear bases de datos
createdb evaltrack_users
mysql -u root -p -e "CREATE DATABASE evaltrack_business;"

# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders
php artisan db:seed
```

6. **Compilar assets**
```bash
npm run build
```

7. **Iniciar servidor**
```bash
php artisan serve
```

### Instalación con Docker

1. **Clonar y configurar**
```bash
git clone https://github.com/tu-usuario/evaltrack.git
cd evaltrack
cp env.example .env
```

2. **Iniciar servicios**
```bash
docker-compose up -d
```

3. **Instalar dependencias y configurar**
```bash
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app npm install
docker-compose exec app npm run build
```

4. **Acceder a la aplicación**
```
http://localhost:8000
```

## 👥 Roles y Permisos

### Administrador
- Gestión completa de usuarios y roles
- Creación y eliminación de eventos
- Acceso a todas las funcionalidades
- Dashboard con estadísticas completas

### Gestor de Talento Humano
- Gestión de empleados (CRUD)
- Gestión de eventos (edición)
- Gestión de asistencias
- Gestión y calificación de evaluaciones

### Empleado
- Visualización de su perfil
- Gestión de sus asistencias
- Confirmación de asistencia a eventos
- Respuesta a evaluaciones

## 📊 Funcionalidades

### Dashboard Personalizado
- **Administrador**: Estadísticas de usuarios, roles, empleados, eventos
- **Gestor**: Próximos eventos, asistencias pendientes, evaluaciones por calificar
- **Empleado**: Asistencias confirmadas/pendientes, evaluaciones, promedio de calificaciones

### Gestión de Eventos
- Creación de eventos con tipos (Capacitación, Reunión, Taller, etc.)
- Estados de eventos (Programado, En Curso, Completado, Cancelado)
- Asignación automática de asistencias

### Control de Asistencias
- Registro automático de asistencias al crear eventos
- Confirmación de asistencia por parte de empleados
- Estados de asistencia (Registrada, Confirmada)
- Creación automática de evaluaciones

### Sistema de Evaluaciones
- Evaluaciones automáticas tras confirmar asistencia
- Preguntas personalizables
- Estados de evaluación (Pendiente, Disponible, Completada, Calificada)
- Sistema de calificación

## 🔧 Configuración

### Variables de Entorno Importantes

```env
# Aplicación
APP_NAME=EvalTrack
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Base de datos PostgreSQL (Usuarios y Roles)
DB_CONNECTION=pgsql
DB_PGSQL_HOST=127.0.0.1
DB_PGSQL_DATABASE=evaltrack_users
DB_PGSQL_USERNAME=evaltrack_user
DB_PGSQL_PASSWORD=password

# Base de datos MySQL (Datos de negocio)
DB_HOST=127.0.0.1
DB_DATABASE=evaltrack_business
DB_USERNAME=evaltrack_user
DB_PASSWORD=password

# Redis (opcional)
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

### Comandos Útiles

```bash
# Limpiar cache
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecutar comandos personalizados
php artisan cleanup:admin-gestor
php artisan cleanup:asistencias-duplicadas
php artisan cleanup:evaluaciones

# Verificar estado de la aplicación
php artisan route:list
php artisan migrate:status
```

## 🧪 Testing

```bash
# Ejecutar tests
php artisan test

# Ejecutar tests con cobertura
php artisan test --coverage

# Ejecutar tests específicos
php artisan test --filter=AsistenciaTest
```

## 📚 Documentación

- [Documentación de DevOps](README_DEVOPS.md)
- [Plan de Despliegue](DEPLOYMENT_PLAN.md)
- [Guía de Contribución](CONTRIBUTING.md)

## 🚀 Despliegue

### Desarrollo Local
```bash
php artisan serve
```

### Docker
```bash
docker-compose up -d
```

### Producción
Ver [README_DEVOPS.md](README_DEVOPS.md) para instrucciones completas de despliegue en Kubernetes.

## 🤝 Contribución

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT - ver el archivo [LICENSE](LICENSE) para detalles.

## 👨‍💻 Equipo

- **Desarrollador Principal**: [Tu Nombre]
- **DevOps Engineer**: [Nombre]
- **UI/UX Designer**: [Nombre]

## 📞 Soporte

- **Email**: soporte@evaltrack.com
- **Documentación**: [Wiki del proyecto](https://github.com/tu-usuario/evaltrack/wiki)
- **Issues**: [GitHub Issues](https://github.com/tu-usuario/evaltrack/issues)

---

**EvalTrack v1.0.0** - Sistema de Gestión de Talento Humano
