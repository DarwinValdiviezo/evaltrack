# Documento de Despliegue DevOps - EvalTrack v1.0.0

## 1. Objetivo y Alcance

### Propósito
Lanzamiento de la versión 1.0.0 del Sistema de Gestión de Talento Humano "EvalTrack" — Plataforma web integral para la administración completa de empleados, eventos corporativos, control de asistencias y sistema de evaluaciones post-evento.

### Ámbito

**Componentes a desplegar:**
- **Aplicación Web:** Laravel 12.0 (PHP 8.2+) con arquitectura MVC.
- **Base de Datos PostgreSQL:** Gestión de usuarios, roles y permisos (Spatie Laravel Permission).
- **Base de Datos MySQL:** Datos de negocio (empleados, eventos, asistencias, evaluaciones).
- **Servidor Web:** Nginx 1.24+ con PHP-FPM 8.2.
- **Cache y Sesiones:** Redis 7.0+ (opcional, para producción).
- **Sistema de Archivos:** Storage para logs, uploads y cache de Laravel.
- **Frontend:** Bootstrap 4.6.2 + SB Admin 2 con Vite para assets.

---

## 2. Arquitectura del Sistema

```
┌─────────────────────────────────────────────────────────────┐
│                    Load Balancer (Nginx)                    │
│                    SSL Termination                          │
└─────────────────────┬───────────────────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────────────────┐
│                    Kubernetes Cluster                       │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────┐  │
│  │   Web Servers   │  │   Web Servers   │  │   Web       │  │
│  │   (PHP-FPM)     │  │   (PHP-FPM)     │  │   Servers   │  │
│  │   Replica 1     │  │   Replica 2     │  │   Replica 3 │  │
│  └─────────────────┘  └─────────────────┘  └─────────────┘  │
└─────────────────────┬───────────────────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────────────────┐
│                    Base de Datos                            │
│  ┌─────────────────┐              ┌─────────────────────┐  │
│  │   PostgreSQL    │              │      MySQL          │  │
│  │  (Users/Roles)  │              │   (Business Data)   │  │
│  │  - users        │              │  - employees        │  │
│  │  - roles        │              │  - eventos          │  │
│  │  - permissions  │              │  - asistencias      │  │
│  │  - model_has_   │              │  - evaluaciones     │  │
│  │    roles        │              │                     │  │
│  └─────────────────┘              └─────────────────────┘  │
└─────────────────────┬───────────────────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────────────────┐
│                    Cache Layer                              │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────┐  │
│  │     Redis       │  │   File Cache    │  │   Session   │  │
│  │   (Sessions)    │  │   (Laravel)     │  │   Storage   │  │
│  └─────────────────┘  └─────────────────┘  └─────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

### Dependencias
**Servicios Externos:**
- **GitHub/GitLab**: Repositorio de código fuente (`https://github.com/company/evaltrack`)
- **Docker Hub**: Registro de imágenes (`docker.io/company/evaltrack`)
- **SSL Certificate Authority**: Let's Encrypt para certificados HTTPS
- **SMTP Server**: Servidor de correo para notificaciones

**Servicios Internos:**
- **Base de datos PostgreSQL 15+**: Gestión de usuarios y roles
- **Base de datos MySQL 8.0+**: Datos de negocio
- **Redis 7.0+**: Cache y sesiones
- **Nginx 1.24+**: Servidor web y proxy reverso
- **PHP-FPM 8.2+**: Procesamiento PHP
- **Kubernetes 1.28+**: Orquestación de contenedores

## 2. Versiones y Artefactos

### Código/Artefacto
- **Nombre del artefacto**: `evaltrack-web:v1.0.0`
- **Repositorio**: `https://github.com/company/evaltrack`
- **Commit/Tag**: `v1.0.0` (commit: `a1b2c3d4e5f6`)
- **Registro Docker**: `docker.io/company/evaltrack:1.0.0`
- **Tamaño estimado**: ~150MB (imagen optimizada)

### Configuraciones Críticas

**Variables de entorno de producción (`prod.env`):**
```env
# =============================================================================
# CONFIGURACIÓN DE LA APLICACIÓN
# =============================================================================
APP_NAME=EvalTrack
APP_ENV=production
APP_KEY=base64:your-app-key-here
APP_DEBUG=false
APP_URL=https://evaltrack.company.com
APP_TIMEZONE=America/Caracas

# =============================================================================
# CONFIGURACIÓN DE BASE DE DATOS
# =============================================================================

# Base de datos PostgreSQL (Usuarios y Roles)
DB_CONNECTION=pgsql
DB_PGSQL_HOST=postgres-prod.company.com
DB_PGSQL_PORT=5432
DB_PGSQL_DATABASE=evaltrack_users
DB_PGSQL_USERNAME=evaltrack_user
DB_PGSQL_PASSWORD=secure_password_here

# Base de datos MySQL (Datos de negocio)
DB_HOST=mysql-prod.company.com
DB_PORT=3306
DB_DATABASE=evaltrack_business
DB_USERNAME=evaltrack_user
DB_PASSWORD=secure_password_here

# =============================================================================
# CONFIGURACIÓN DE CACHE Y SESIONES
# =============================================================================
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis (producción)
REDIS_HOST=redis-prod.company.com
REDIS_PASSWORD=redis_password_here
REDIS_PORT=6379

# =============================================================================
# CONFIGURACIÓN DE EMAIL
# =============================================================================
MAIL_MAILER=smtp
MAIL_HOST=smtp.company.com
MAIL_PORT=587
MAIL_USERNAME=noreply@company.com
MAIL_PASSWORD=mail_password_here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@company.com
MAIL_FROM_NAME="EvalTrack System"

# =============================================================================
# CONFIGURACIÓN DE LOGS
# =============================================================================
LOG_CHANNEL=stack
LOG_LEVEL=error

# =============================================================================
# CONFIGURACIÓN DE SEGURIDAD
# =============================================================================
RATE_LIMIT_API=60,1
RATE_LIMIT_LOGIN=5,1

# =============================================================================
# CONFIGURACIÓN DE MONITORING
# =============================================================================
PROMETHEUS_ENABLED=true
PROMETHEUS_NAMESPACE=evaltrack
```

**Secrets (Kubernetes Secrets / HashiCorp Vault):**
- `evaltrack-postgres-password`: Contraseña PostgreSQL
- `evaltrack-mysql-password`: Contraseña MySQL
- `evaltrack-redis-password`: Contraseña Redis
- `evaltrack-app-key`: Clave de aplicación Laravel
- `evaltrack-mail-password`: Contraseña SMTP
- `evaltrack-ssl-cert`: Certificado SSL
- `evaltrack-ssl-key`: Clave privada SSL
- `docker-registry-secret`: Credenciales Docker Hub

## 3. Entornos y Pipeline CI/CD

### Flujo de Despliegue
```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   Develop   │───▶│     QA      │───▶│   Staging   │───▶│ Production  │
│   (GitHub)  │    │  (Testing)  │    │ (Pre-prod)  │    │   (Live)    │
└─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘
       │                   │                   │                   │
       ▼                   ▼                   ▼                   ▼
   Auto Build          Auto Deploy         Manual Deploy      Blue/Green
   + Tests             + Smoke Tests       + Integration      + Rollback
```

**Etapas del Pipeline:**
1. **Develop** → Build automático en cada push + tests unitarios
2. **QA** → Despliegue automático tras merge a `develop` + tests de integración
3. **Staging** → Despliegue manual tras aprobación QA + tests de aceptación
4. **Production** → Despliegue manual tras aprobación Staging + Blue/Green

**Enlace al Pipeline**: `https://github.com/company/evaltrack/actions`

### Estrategia de Despliegue

**Blue/Green Deployment (Producción):**
- **Blue Environment**: Versión actual en producción (3 réplicas)
- **Green Environment**: Nueva versión a desplegar (inicialmente 0 réplicas)
- **Switch**: Cambio de tráfico instantáneo tras validación completa
- **Rollback**: 5 minutos máximo en caso de fallo

**Rolling Update (Desarrollo/Staging):**
- Actualización gradual de pods
- Health checks automáticos
- Rollback automático si falla

**Ventana de Mantenimiento:**
- **Día**: Domingo
- **Hora**: 02:00 - 04:00 AM (GMT-4)
- **Duración estimada**: 30 minutos
- **Tiempo de inactividad**: 0 minutos (Blue/Green)

### Configuración de GitHub Actions

**Pipeline Principal** (`.github/workflows/ci-cd.yml`):
```yaml
name: CI/CD Pipeline - EvalTrack
on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main, develop ]

jobs:
  test:
    name: Tests y Análisis de Código
    runs-on: ubuntu-latest
    services:
      postgres: postgres:15
      mysql: mysql:8.0
      redis: redis:7-alpine
    
  build:
    name: Construir Imagen Docker
    needs: test
    runs-on: ubuntu-latest
    
  deploy-dev:
    name: Desplegar a Desarrollo
    needs: build
    runs-on: ubuntu-latest
    environment: development
    
  deploy-staging:
    name: Desplegar a Staging
    needs: [build, deploy-dev]
    runs-on: ubuntu-latest
    environment: staging
    
  deploy-production:
    name: Desplegar a Producción
    needs: [build, deploy-staging]
    runs-on: ubuntu-latest
    environment: production
```

## 4. Configuración de Infraestructura

### Docker Compose (Desarrollo Local)
```yaml
version: '3.8'
services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: evaltrack-app
    restart: unless-stopped
    ports:
      - "8000:80"
    environment:
      - APP_ENV=local
      - APP_DEBUG=true
      - DB_CONNECTION=pgsql
      - DB_PGSQL_HOST=postgres
      - DB_PGSQL_DATABASE=evaltrack_users
      - DB_PGSQL_USERNAME=evaltrack_user
      - DB_PGSQL_PASSWORD=password
      - DB_HOST=mysql
      - DB_DATABASE=evaltrack_business
      - DB_USERNAME=evaltrack_user
      - DB_PASSWORD=password
      - REDIS_HOST=redis
    volumes:
      - ./storage:/var/www/html/storage
      - ./bootstrap/cache:/var/www/html/bootstrap/cache
    depends_on:
      - postgres
      - mysql
      - redis

  postgres:
    image: postgres:15-alpine
    container_name: evaltrack-postgres
    environment:
      POSTGRES_DB: evaltrack_users
      POSTGRES_USER: evaltrack_user
      POSTGRES_PASSWORD: password
    ports:
      - "5432:5432"
    volumes:
      - postgres_data:/var/lib/postgresql/data

  mysql:
    image: mysql:8.0
    container_name: evaltrack-mysql
    environment:
      MYSQL_DATABASE: evaltrack_business
      MYSQL_USER: evaltrack_user
      MYSQL_PASSWORD: password
      MYSQL_ROOT_PASSWORD: root_password
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql

  redis:
    image: redis:7-alpine
    container_name: evaltrack-redis
    ports:
      - "6379:6379"
    volumes:
      - redis_data:/data

volumes:
  postgres_data:
  mysql_data:
  redis_data:
```

### Kubernetes (Producción)

**Deployment Principal** (`k8s/production/deployment.yaml`):
```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: evaltrack-web
  namespace: evaltrack-prod
  labels:
    app: evaltrack-web
    version: "1.0.0"
spec:
  replicas: 3
  strategy:
    type: RollingUpdate
    rollingUpdate:
      maxSurge: 1
      maxUnavailable: 0
  selector:
    matchLabels:
      app: evaltrack-web
  template:
    metadata:
      labels:
        app: evaltrack-web
        version: "1.0.0"
    spec:
      containers:
      - name: evaltrack
        image: company/evaltrack:1.0.0
        imagePullPolicy: Always
        ports:
        - containerPort: 80
        env:
        - name: APP_ENV
          value: "production"
        - name: APP_DEBUG
          value: "false"
        - name: APP_URL
          value: "https://evaltrack.company.com"
        # ... resto de variables de entorno
        resources:
          requests:
            memory: "256Mi"
            cpu: "250m"
          limits:
            memory: "512Mi"
            cpu: "500m"
        livenessProbe:
          httpGet:
            path: /health
            port: 80
          initialDelaySeconds: 30
          periodSeconds: 10
        readinessProbe:
          httpGet:
            path: /health
            port: 80
          initialDelaySeconds: 5
          periodSeconds: 5
```

**Blue/Green Deployment** (`k8s/production/blue-green-deployment.yaml`):
```yaml
# Blue Environment (Versión actual)
apiVersion: apps/v1
kind: Deployment
metadata:
  name: evaltrack-web-blue
  namespace: evaltrack-prod
  labels:
    app: evaltrack-web
    environment: blue
    version: "1.0.0"
spec:
  replicas: 3
  # ... configuración completa

---
# Green Environment (Nueva versión)
apiVersion: apps/v1
kind: Deployment
metadata:
  name: evaltrack-web-green
  namespace: evaltrack-prod
  labels:
    app: evaltrack-web
    environment: green
    version: "1.1.0"
spec:
  replicas: 0  # Inicialmente sin réplicas
  # ... configuración completa
```

## 5. Funcionalidades del Sistema

### Módulos Principales

**1. Gestión de Usuarios y Roles:**
- Sistema de autenticación Laravel UI
- Roles: Administrador, Gestor de Talento Humano, Empleado
- Permisos granulares con Spatie Laravel Permission
- CRUD completo de usuarios y roles

**2. Gestión de Empleados:**
- Perfiles detallados de empleados
- Estados: Activo, Inactivo
- Relación con usuarios del sistema
- Gestión de información personal y laboral

**3. Gestión de Eventos:**
- Tipos: Capacitación, Reunión, Taller, Conferencia, Otro
- Estados: Programado, En Curso, Completado, Cancelado, Borrador
- Asignación automática de asistencias
- Creación automática de evaluaciones

**4. Control de Asistencias:**
- Registro automático al crear eventos
- Confirmación por parte de empleados
- Estados: Registrada, Confirmada
- Creación automática de evaluaciones

**5. Sistema de Evaluaciones:**
- Evaluaciones automáticas post-asistencia
- Preguntas personalizables
- Estados: Pendiente, Disponible, Completada, Calificada
- Sistema de calificación y feedback

**6. Dashboard Personalizado:**
- **Administrador**: Estadísticas completas del sistema
- **Gestor**: Próximos eventos, asistencias pendientes
- **Empleado**: Asistencias personales, evaluaciones, promedio

### Rutas Principales
```php
// Autenticación
Auth::routes();

// Dashboard
Route::get('/home', [HomeController::class,'index'])->middleware('auth');

// Gestión de empleados
Route::resource('empleados', EmpleadoController::class);

// Gestión de eventos
Route::resource('eventos', EventoController::class);

// Control de asistencias
Route::resource('asistencias', AsistenciaController::class);

// Sistema de evaluaciones
Route::resource('evaluaciones', EvaluacionController::class);

// Gestión de usuarios y roles
Route::resource('users', UserController::class);
Route::resource('roles', RoleController::class);
```

## 6. Monitoreo y Logging

### Script de Monitoreo (`scripts/monitor.sh`)
```bash
#!/bin/bash
# Script de monitoreo para EvalTrack

# Funciones de monitoreo:
# - health_check(): Verificación de salud de la aplicación
# - database_check(): Conectividad de bases de datos
# - services_check(): Estado de servicios del sistema
# - disk_check(): Espacio en disco
# - memory_check(): Uso de memoria
# - logs_check(): Análisis de logs de errores
# - performance_check(): Rendimiento de la aplicación
# - ssl_check(): Verificación de certificados SSL
```

### Métricas a Monitorear

**Aplicación:**
- Response time promedio (< 1s)
- Error rate (< 1%)
- Throughput (requests/segundo)
- Uso de memoria y CPU
- Conexiones de base de datos

**Infraestructura:**
- CPU, memoria, disco (< 80% uso)
- Latencia de red
- Disponibilidad de servicios (> 99.9%)
- Uso de recursos Kubernetes

**Negocio:**
- Usuarios activos
- Eventos creados
- Asistencias registradas
- Evaluaciones completadas

## 7. Seguridad

### Configuraciones de Seguridad

**Kubernetes:**
- Pod Security Policies
- Network Policies
- RBAC (Role-Based Access Control)
- Secrets management

**Aplicación:**
- HTTPS obligatorio
- Headers de seguridad (X-Frame-Options, X-XSS-Protection)
- Rate limiting (API: 60 req/min, Login: 5 req/min)
- Validación de entrada y sanitización
- CSRF protection

**Base de Datos:**
- Conexiones SSL
- Usuarios con permisos mínimos
- Backup encriptado
- Auditoría de accesos

## 8. Backup y Recuperación

### Estrategia de Backup

**Base de Datos:**
- **PostgreSQL**: Backup diario completo + WAL archiving
- **MySQL**: Backup diario completo + binlog
- **Retención**: 30 días
- **Frecuencia**: Diario a las 02:00 AM

**Archivos:**
- Storage de Laravel (uploads, logs)
- Configuraciones
- **Retención**: 7 días
- **Frecuencia**: Diario

### Script de Backup (`scripts/backup.sh`)
```bash
#!/bin/bash
# Script de backup para EvalTrack

# Backup PostgreSQL
pg_dump -h $POSTGRES_HOST -U $POSTGRES_USER $POSTGRES_DB > backup/postgres_$(date +%Y%m%d_%H%M%S).sql

# Backup MySQL
mysqldump -h $MYSQL_HOST -u $MYSQL_USER -p$MYSQL_PASSWORD $MYSQL_DB > backup/mysql_$(date +%Y%m%d_%H%M%S).sql

# Backup archivos
tar -czf backup/files_$(date +%Y%m%d_%H%M%S).tar.gz storage/
```

## 9. Comandos de Despliegue

### Despliegue Manual
```bash
# Despliegue a desarrollo
./scripts/deploy.sh development 1.0.0

# Despliegue a staging
./scripts/deploy.sh staging 1.0.0

# Despliegue a producción
./scripts/deploy.sh production 1.0.0
```

### Despliegue con kubectl
```bash
# Actualizar imagen
kubectl set image deployment/evaltrack-web evaltrack=company/evaltrack:1.0.0 -n evaltrack-prod

# Verificar rollout
kubectl rollout status deployment/evaltrack-web -n evaltrack-prod

# Rollback si es necesario
kubectl rollout undo deployment/evaltrack-web -n evaltrack-prod
```

### Docker Compose (Desarrollo Local)
```bash
# Iniciar todos los servicios
docker-compose up -d

# Ver logs
docker-compose logs -f app

# Ejecutar migraciones
docker-compose exec app php artisan migrate:all

# Ejecutar seeders
docker-compose exec app php artisan db:seed
```

## 10. Troubleshooting

### Problemas Comunes

**1. Error de conexión a base de datos:**
```bash
# Verificar conectividad
kubectl exec -it deployment/evaltrack-web -n evaltrack-prod -- php artisan tinker
# Probar conexión: DB::connection()->getPdo();
```

8. **Iniciar el servidor**
   ```bash
   php artisan serve
   ```

9. **Acceder a la aplicación**
   - [http://localhost:8000](http://localhost:8000)

---

## 6. Buenas Prácticas

- Mantén tus variables de entorno y secrets fuera del control de versiones.
- Realiza backups periódicos de ambas bases de datos.
- Utiliza `php artisan config:clear` y `php artisan cache:clear` tras cambios en configuración.
- Consulta la documentación oficial en el repositorio para detalles avanzados.

---

**Documento generado**: Diciembre 2024  
**Versión del documento**: 1.0.0  
**Responsable**: Equipo DevOps  
**Revisión**: Anual 
