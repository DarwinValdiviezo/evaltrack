# Plan de Despliegue de Producción - Sistema de Gestión de Talento Humano

## 1. Objetivo y Alcance

### Propósito
**Migración y Lanzamiento de EvalTrack v2.0.0** - Actualización del Sistema de Gestión de Talento Humano desde Laravel 12.0 a NestJS/React, manteniendo toda la funcionalidad existente y agregando mejoras de performance, escalabilidad y experiencia de usuario.

### Ámbito
**Componentes a desplegar:**
- **Backend API v2.0**: NestJS con Prisma ORM (migración desde Laravel)
  - Servicios: Usuarios, Eventos, Asistencias, Evaluaciones, Autenticación
  - Base de datos: PostgreSQL (unificada desde PostgreSQL + MySQL)
  - ORM: Prisma Client (migración desde Eloquent)
- **Frontend Web v2.0**: React + Vite + TypeScript (migración desde Blade)
  - Componentes: Formularios, Tablas, Dashboard
  - Autenticación JWT (migración desde Laravel UI)
  - Responsive design con Tailwind CSS (migración desde Bootstrap)
- **Base de Datos**: PostgreSQL unificada
  - Migración de datos desde v1.0.0 (Laravel)
  - Tablas: users, events, attendances, evaluations, questions, answers
  - Migraciones: Prisma migrations
  - Seeders: Datos de prueba + migración de datos existentes

### Dependencias
**Servicios Externos:**
- PostgreSQL Database (producción v2.0)
- Docker Hub (para imágenes de contenedores)
- **Legacy System**: EvalTrack v1.0.0 (Laravel) para migración de datos

**Servicios Internos:**
- API Gateway (opcional para futuras expansiones)
- Sistema de logs centralizado
- Monitoreo de métricas
- **Scripts de Migración**: Herramientas para migrar datos desde v1.0.0

### Arquitectura del Sistema

**Evolución Arquitectónica:**
```
v1.0.0 (Laravel)                    v2.0.0 (NestJS/React)
┌─────────────────┐                  ┌─────────────────┐
│   Laravel App   │                  │   React SPA      │
│   (Blade +      │                  │   (TypeScript +  │
│    Bootstrap)   │                  │   Tailwind)      │
└─────────────────┘                  └─────────────────┘
         │                                    │
         ▼                                    ▼
┌─────────────────┐                  ┌─────────────────┐
│   Laravel API   │                  │   NestJS API     │
│   (Eloquent)    │                  │   (Prisma)       │
└─────────────────┘                  └─────────────────┘
         │                                    │
         ▼                                    ▼
┌─────────────────┐                  ┌─────────────────┐
│ PostgreSQL +    │                  │   PostgreSQL     │
│ MySQL           │                  │   (Unificada)    │
└─────────────────┘                  └─────────────────┘
```

**Nueva Arquitectura v2.0:**
```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │   Backend API   │    │   PostgreSQL    │
│   (React/Vite)  │◄──►│   (NestJS)      │◄──►│   Database      │
│                 │    │                 │    │                 │
│ - Dashboard     │    │ - Auth Service  │    │ - Users         │
│ - Forms         │    │ - User Service  │    │ - Events        │
│ - Tables        │    │ - Event Service │    │ - Attendances   │
│ - Auth          │    │ - Evaluation    │    │ - Evaluations   │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

### Funcionalidades Principales
**Mantenidas desde v1.0.0:**
- **Gestión de Usuarios**: CRUD con roles y autenticación
- **Gestión de Eventos**: Creación, edición, preguntas asociadas
- **Control de Asistencias**: Registro y seguimiento de asistencia
- **Sistema de Evaluaciones**: Creación, respuesta y calificación
- **Dashboard**: Visualización de métricas por rol

**Mejoras en v2.0.0:**
- **Autenticación JWT**: Seguridad mejorada y stateless
- **API RESTful**: Arquitectura más escalable
- **Frontend React**: Mejor experiencia de usuario
- **Base de Datos Unificada**: Simplificación de infraestructura
- **Performance**: Mejor rendimiento y tiempos de respuesta
- **Escalabilidad**: Preparado para microservicios

## 2. Versiones y Artefactos

### Código/Artefacto

**Evolución del Sistema:**
- **Versión Anterior**: `evaltrack:v1.0.0` (Laravel 12.0 + PHP 8.2)
  - Repositorio: https://github.com/DarwinValdiviezo/evaltrack.git
  - Tecnologías: Laravel, Bootstrap, PostgreSQL + MySQL
  - Estado: Legacy (migración completada)

**Nueva Versión**: `evaltrack:v2.0.0` (NestJS + React)
- **Nombre y versión**: `evaltrack-api:v2.0.0` (Backend)
- **Repositorio**: Git commit/tag: `v2.0.0-prod`
- **Registro Docker**: `dockerhub.com/darwinvaldiviezo/evaltrack-api:v2.0.0`
- **Tecnologías**: NestJS v11.0.1, Prisma v6.11.1, PostgreSQL

**Frontend Web:**
- **Nombre y versión**: `evaltrack-frontend:v2.0.0`
- **Repositorio**: Git commit/tag: `v2.0.0-prod`
- **Registro Docker**: `dockerhub.com/darwinvaldiviezo/evaltrack-frontend:v2.0.0`
- **Tecnologías**: React 18, Vite, TypeScript, Tailwind CSS

**Base de Datos:**
- **Versión PostgreSQL**: 15.x o superior
- **Migración**: `20250707050328_init` (migración consolidada)
- **Seed**: Datos de prueba incluidos
- **Migración de Datos**: Script de migración desde Laravel v1.0.0

### Comparación de Versiones

| Aspecto | v1.0.0 (Laravel) | v2.0.0 (NestJS/React) |
|---------|------------------|----------------------|
| **Backend** | Laravel 12.0 (PHP) | NestJS 11.0 (TypeScript) |
| **Frontend** | Blade + Bootstrap | React + Tailwind CSS |
| **Base de Datos** | PostgreSQL + MySQL | PostgreSQL (unificada) |
| **ORM** | Eloquent | Prisma |
| **Autenticación** | Laravel UI | JWT + Passport |
| **Arquitectura** | Monolítica | API + SPA |
| **Performance** | Server-side rendering | Client-side rendering |
| **Escalabilidad** | Vertical | Horizontal + Microservicios |

### Configuraciones

**Variables de Entorno Críticas (`prod.env`):**
```env
# Aplicación
APP_NAME=EvalTrack v2.0
NODE_ENV=production
PORT=3000

# Base de Datos (Migrada desde v1.0.0)
DATABASE_URL="postgresql://user:password@host:5432/evaltrack_v2_prod"

# JWT
JWT_SECRET="tu-jwt-secret-super-seguro-produccion-v2"
JWT_EXPIRES_IN="24h"

# CORS
FRONTEND_URL="https://evaltrack.tuempresa.com"

# Logs
LOG_LEVEL="info"

# Migración de Datos
MIGRATION_FROM_V1=true
LEGACY_DB_URL="postgresql://user:password@host:5432/evaltrack_v1_prod"
```

**Secrets (Gestión Segura):**
- **JWT_SECRET**: Clave secreta para firmar tokens JWT
- **DATABASE_PASSWORD**: Contraseña de la base de datos v2.0
- **LEGACY_DB_PASSWORD**: Contraseña de la base de datos v1.0 (migración)
- **API_KEYS**: Claves de servicios externos
- **SSL_CERTIFICATES**: Certificados SSL/TLS para HTTPS

### Artefactos de Despliegue

**Docker Images:**
```bash
# Backend v2.0
docker build -t evaltrack-api:v2.0.0 ./backend
docker push dockerhub.com/darwinvaldiviezo/evaltrack-api:v2.0.0

# Frontend v2.0
docker build -t evaltrack-frontend:v2.0.0 ./frontend
docker push dockerhub.com/darwinvaldiviezo/evaltrack-frontend:v2.0.0

# Imagen de migración de datos
docker build -t evaltrack-migration:v2.0.0 ./scripts/migration
docker push dockerhub.com/darwinvaldiviezo/evaltrack-migration:v2.0.0
```

**Docker Compose (Producción):**
- `docker-compose.prod.yml` - Configuración de producción v2.0
- `Dockerfile` - Imágenes optimizadas para producción
- `.dockerignore` - Archivos excluidos del build

**Scripts de Despliegue:**
- `scripts/deploy.sh` - Script principal de despliegue v2.0
- `scripts/migrate-data.sh` - Migración de datos desde v1.0.0
- `scripts/setup-db.sh` - Configuración inicial de base de datos
- `scripts/backup.sh` - Backup de datos antes del despliegue
- `scripts/rollback-v1.sh` - Rollback a versión Laravel si es necesario

### Plan de Migración de Datos

**Script de Migración (`scripts/migrate-data.sh`):**
```bash
#!/bin/bash
set -e

echo "🔄 Iniciando migración de datos desde EvalTrack v1.0.0..."

# Variables
LEGACY_DB_URL=$LEGACY_DB_URL
NEW_DB_URL=$DATABASE_URL
MIGRATION_LOG="/logs/migration_$(date +%Y%m%d_%H%M%S).log"

# 1. Backup de ambas bases de datos
echo "📦 Creando backups..."
pg_dump $LEGACY_DB_URL > /backups/evaltrack_v1_backup.sql
pg_dump $NEW_DB_URL > /backups/evaltrack_v2_backup.sql

# 2. Migrar usuarios y roles
echo "👥 Migrando usuarios y roles..."
node scripts/migration/migrate-users.js

# 3. Migrar empleados
echo "👤 Migrando empleados..."
node scripts/migration/migrate-employees.js

# 4. Migrar eventos
echo "📅 Migrando eventos..."
node scripts/migration/migrate-events.js

# 5. Migrar asistencias
echo "✅ Migrando asistencias..."
node scripts/migration/migrate-attendances.js

# 6. Migrar evaluaciones
echo "📊 Migrando evaluaciones..."
node scripts/migration/migrate-evaluations.js

# 7. Verificar integridad
echo "🔍 Verificando integridad de datos..."
node scripts/migration/verify-migration.js

echo "✅ Migración completada exitosamente!"
echo "📋 Log de migración: $MIGRATION_LOG"
```

**Validación de Migración:**
- ✅ Usuarios migrados con roles preservados
- ✅ Empleados con perfiles completos
- ✅ Eventos con estados correctos
- ✅ Asistencias con confirmaciones
- ✅ Evaluaciones con calificaciones
- ✅ Integridad referencial verificada

## 3. Entornos y Pipeline CI/CD

### Flujo de Despliegue

**Etapas Validadas:**
```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   Desarrollo│    │     QA      │    │   Staging   │    │ Producción  │
│   (Local)   │───►│   Testing   │───►│   Pre-Prod  │───►│   (Prod)    │
└─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘
```

**Enlaces al Pipeline:**
- **GitHub Actions**: `.github/workflows/deploy.yml`
- **Docker Hub**: Registro automático de imágenes
- **Slack Notifications**: Canal `#deployments`

### Estrategia de Despliegue

**Para Contenedores Docker:**
- **Blue/Green Deployment**: 
  - Versión actual (Blue) → Nueva versión (Green)
  - Switch de tráfico instantáneo
  - Rollback rápido si hay problemas

**Para Base de Datos:**
- **Migraciones Graduales**: 
  - Backup antes de migración
  - Migración en ventana de mantenimiento
  - Rollback de migración si es necesario

**Ventana de Mantenimiento:**
- **Día**: Domingo
- **Hora**: 02:00 - 04:00 AM (UTC-5)
- **Duración**: 2 horas máximo
- **Notificación**: 24h antes por email y Slack

### Pipeline CI/CD Detallado

**GitHub Actions Workflow:**
```yaml
name: Deploy to Production
on:
  push:
    tags:
      - 'v*'

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - name: Run Tests
        run: npm test
      
      - name: Build Backend
        run: npm run build
        
      - name: Build Frontend
        run: npm run build

  build-and-push:
    needs: test
    runs-on: ubuntu-latest
    steps:
      - name: Build Docker Images
        run: |
          docker build -t talent-management-api:${{ github.ref_name }} ./backend
          docker build -t talent-management-frontend:${{ github.ref_name }} ./frontend
      
      - name: Push to Docker Hub
        run: |
          docker push talent-management-api:${{ github.ref_name }}
          docker push talent-management-frontend:${{ github.ref_name }}

  deploy:
    needs: build-and-push
    runs-on: ubuntu-latest
    steps:
      - name: Deploy to Production
        run: |
          ssh user@server "cd /app && ./scripts/deploy.sh ${{ github.ref_name }}"
```

**Validaciones Automáticas:**
- ✅ Tests unitarios pasan
- ✅ Tests de integración pasan
- ✅ Build exitoso
- ✅ Imágenes Docker creadas
- ✅ Health checks pasan
- ✅ Smoke tests en staging

## 4. Plan de Ejecución

### Pasos Automatizados

**Orden de Despliegue:**
1. **Backup de Base de Datos** (5 min)
2. **Despliegue de Base de Datos** (10 min)
3. **Despliegue de Backend API** (15 min)
4. **Despliegue de Frontend** (10 min)
5. **Validaciones Post-Despliegue** (10 min)

**Comandos Clave de Despliegue:**

```bash
# 1. Backup de BD
./scripts/backup.sh

# 2. Migración de BD
npx prisma migrate deploy
npx prisma generate

# 3. Despliegue Backend
docker-compose -f docker-compose.prod.yml up -d backend

# 4. Despliegue Frontend
docker-compose -f docker-compose.prod.yml up -d frontend

# 5. Health Checks
curl -f http://localhost:3000/health
curl -f http://localhost:5173/
```

**Script Principal de Despliegue (`scripts/deploy.sh`):**
```bash
#!/bin/bash
set -e

VERSION=$1
echo "🚀 Iniciando despliegue de versión: $VERSION"

# Variables
BACKUP_DIR="/backups/$(date +%Y%m%d_%H%M%S)"
COMPOSE_FILE="docker-compose.prod.yml"

# 1. Backup
echo "📦 Creando backup..."
mkdir -p $BACKUP_DIR
pg_dump $DATABASE_URL > $BACKUP_DIR/backup.sql

# 2. Pull nuevas imágenes
echo "⬇️ Descargando nuevas imágenes..."
docker-compose -f $COMPOSE_FILE pull

# 3. Migración de BD
echo "🗄️ Ejecutando migraciones..."
docker-compose -f $COMPOSE_FILE run --rm backend npx prisma migrate deploy

# 4. Despliegue Blue/Green
echo "🔄 Desplegando con estrategia Blue/Green..."
docker-compose -f $COMPOSE_FILE up -d --no-deps backend
sleep 30

# 5. Health Check Backend
echo "🏥 Verificando salud del backend..."
for i in {1..10}; do
  if curl -f http://localhost:3000/health; then
    echo "✅ Backend saludable"
    break
  fi
  sleep 10
done

# 6. Despliegue Frontend
echo "🎨 Desplegando frontend..."
docker-compose -f $COMPOSE_FILE up -d --no-deps frontend
sleep 20

# 7. Health Check Frontend
echo "🏥 Verificando salud del frontend..."
if curl -f http://localhost:5173/; then
  echo "✅ Frontend saludable"
else
  echo "❌ Frontend no responde"
  exit 1
fi

echo "🎉 Despliegue completado exitosamente!"
```

### Validaciones

**Pruebas Post-Despliegue (Smoke Tests):**
```bash
# 1. Health Check API
curl -f http://localhost:3000/health

# 2. Login Test
curl -X POST http://localhost:3000/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@test.com","password":"admin123"}'

# 3. Frontend Load Test
curl -f http://localhost:5173/

# 4. Database Connection Test
docker-compose exec backend npx prisma db execute --stdin <<< "SELECT 1;"
```

**Monitoreo Inicial (Primeros 30 minutos):**
- **Métricas de CPU**: < 80% promedio
- **Métricas de Memoria**: < 85% promedio
- **Latencia de API**: < 500ms promedio
- **Error Rate**: < 1% de requests
- **Logs**: Sin errores críticos

**Alertas Automáticas:**
- CPU > 90% por 5 minutos
- Memoria > 95% por 5 minutos
- Error rate > 5% por 5 minutos
- Health check falla por 3 intentos consecutivos 

## 5. Rollback y Mitigación de Riesgos

### Condiciones de Fallo

**Umbrales de Error (Triggers de Rollback):**
- **Error Rate**: > 5% de requests fallidas en 5 minutos
- **Latencia**: > 2 segundos promedio en 5 minutos
- **Health Check**: Fallo por 3 intentos consecutivos
- **CPU/Memoria**: > 95% por más de 10 minutos
- **Base de Datos**: Conexiones fallidas > 10% en 5 minutos

**Indicadores de Fallo Crítico:**
- ✅ **Verde**: Todo funcionando correctamente
- 🟡 **Amarillo**: Degradación de servicio (monitoreo intensivo)
- 🔴 **Rojo**: Fallo crítico (rollback automático)

### Procedimiento de Rollback

**Rollback Automático (Script `scripts/rollback.sh`):**
```bash
#!/bin/bash
set -e

echo "🔄 Iniciando rollback automático..."

# Variables
COMPOSE_FILE="docker-compose.prod.yml"
BACKUP_DIR="/backups/$(ls -t /backups | head -1)"

# 1. Detener servicios actuales
echo "⏹️ Deteniendo servicios actuales..."
docker-compose -f $COMPOSE_FILE down

# 2. Restaurar versión anterior
echo "⬅️ Restaurando versión anterior..."
docker-compose -f $COMPOSE_FILE up -d backend:previous
docker-compose -f $COMPOSE_FILE up -d frontend:previous

# 3. Health Check
echo "🏥 Verificando salud después del rollback..."
sleep 30
if curl -f http://localhost:3000/health; then
  echo "✅ Rollback exitoso"
else
  echo "❌ Rollback falló - Restaurando backup de BD"
  # Restaurar backup de BD si es necesario
  psql $DATABASE_URL < $BACKUP_DIR/backup.sql
fi

# 4. Notificar al equipo
echo "📢 Enviando notificación de rollback..."
curl -X POST $SLACK_WEBHOOK \
  -H "Content-Type: application/json" \
  -d '{"text":"🚨 ROLLBACK AUTOMÁTICO EJECUTADO - Sistema restaurado a versión anterior"}'
```

**Rollback Manual (Comandos de Emergencia):**
```bash
# Rollback rápido de contenedores
docker-compose -f docker-compose.prod.yml down
docker-compose -f docker-compose.prod.yml up -d backend:previous frontend:previous

# Rollback de base de datos (si es necesario)
pg_restore $DATABASE_URL < /backups/backup_$(date -d "1 hour ago" +%Y%m%d_%H%M%S).sql

# Verificar estado
docker-compose -f docker-compose.prod.yml ps
curl -f http://localhost:3000/health
```

### Backup de Datos

**Estrategia de Backup:**
- **Frecuencia**: Antes de cada despliegue + diario a las 2:00 AM
- **Retención**: 30 días de backups
- **Ubicación**: `/backups/` en servidor + copia en S3/Cloud Storage
- **Verificación**: Test de restauración semanal

**Script de Backup (`scripts/backup.sh`):**
```bash
#!/bin/bash
set -e

BACKUP_DIR="/backups/$(date +%Y%m%d_%H%M%S)"
S3_BUCKET="talent-management-backups"

echo "📦 Iniciando backup..."

# Crear backup local
mkdir -p $BACKUP_DIR
pg_dump $DATABASE_URL > $BACKUP_DIR/backup.sql

# Comprimir backup
gzip $BACKUP_DIR/backup.sql

# Subir a S3 (si está configurado)
if [ ! -z "$AWS_ACCESS_KEY_ID" ]; then
  aws s3 cp $BACKUP_DIR/backup.sql.gz s3://$S3_BUCKET/
  echo "☁️ Backup subido a S3"
fi

# Limpiar backups antiguos (mantener solo 30 días)
find /backups -name "*.sql.gz" -mtime +30 -delete

echo "✅ Backup completado: $BACKUP_DIR/backup.sql.gz"
```

### Plan de Contingencia

**Escenarios de Emergencia:**

1. **Fallo Total del Servidor:**
   - Activar servidor de respaldo
   - Restaurar desde último backup
   - Tiempo de recuperación: 15-30 minutos

2. **Fallo de Base de Datos:**
   - Conectar a réplica de lectura
   - Restaurar desde backup más reciente
   - Tiempo de recuperación: 10-20 minutos

3. **Fallo de Red:**
   - Activar CDN de respaldo
   - Redirigir tráfico a servidor secundario
   - Tiempo de recuperación: 5-10 minutos

**Contactos de Emergencia:**
- **DevOps Lead**: +1-555-0101 (24/7)
- **DBA**: +1-555-0102 (8AM-6PM)
- **Sistemas**: +1-555-0103 (24/7)
- **Slack**: #incidents-prod 

## 6. Equipo y Comunicación

### Responsables

**Dueño del Despliegue:**
- **Nombre**: DevOps Engineer
- **Rol**: Lead DevOps Engineer
- **Email**: devops@tuempresa.com
- **Teléfono**: +1-555-0101
- **Horario**: 24/7 (on-call)

**Contacto de Emergencia:**
- **Nombre**: Systems Administrator
- **Rol**: Senior Systems Admin
- **Email**: systems@tuempresa.com
- **Teléfono**: +1-555-0103
- **Horario**: 24/7 (on-call)

**Equipo de Soporte:**
- **Backend Developer**: backend@tuempresa.com
- **Frontend Developer**: frontend@tuempresa.com
- **Database Administrator**: dba@tuempresa.com
- **Product Owner**: product@tuempresa.com

### Notificaciones

**Canales de Comunicación:**
- **Slack**: 
  - `#deployments` - Notificaciones de despliegue
  - `#incidents-prod` - Incidentes de producción
  - `#devops-alerts` - Alertas automáticas
- **Email**: 
  - `deployments@tuempresa.com` - Lista de distribución
  - `alerts@tuempresa.com` - Alertas críticas
- **Jira**: 
  - Proyecto `DEPLOY` - Tickets de despliegue
  - Proyecto `INCIDENT` - Tickets de incidentes

**Plantillas de Notificación:**

**Pre-Despliegue:**
```
🚀 DESPLIEGUE PROGRAMADO
Sistema: EvalTrack v2.0.0 (Migración desde Laravel v1.0.0)
Fecha: [FECHA]
Hora: 02:00 AM (UTC-5)
Duración estimada: 3 horas (incluye migración de datos)
Equipo responsable: DevOps Team
```

**Post-Despliegue Exitoso:**
```
✅ DESPLIEGUE COMPLETADO
Sistema: EvalTrack v2.0.0
Estado: EXITOSO
Tiempo total: [DURACIÓN]
Migración de datos: COMPLETADA
Métricas iniciales: [LINK_DASHBOARD]
```

**Rollback:**
```
🚨 ROLLBACK EJECUTADO
Sistema: EvalTrack v2.0.0
Razón: [MOTIVO]
Estado: RESTAURADO a v1.0.0 (Laravel)
Próximos pasos: [ACCIONES]
```

## 7. Checklist Pre-Despliegue

### Validaciones Técnicas

**✅ Pruebas Aprobadas:**
- [ ] Tests unitarios pasan (100% coverage)
- [ ] Tests de integración pasan
- [ ] Tests end-to-end en staging
- [ ] Performance tests aprobados
- [ ] Security scan sin vulnerabilidades críticas

**✅ Infraestructura:**
- [ ] Servidor de producción disponible
- [ ] Base de datos con espacio suficiente
- [ ] DNS configurado correctamente
- [ ] SSL certificates válidos
- [ ] Load balancer configurado

**✅ Configuración:**
- [ ] Variables de entorno actualizadas
- [ ] Secrets rotados y seguros
- [ ] Logs configurados
- [ ] Monitoreo activo
- [ ] Alertas configuradas

### Validaciones de Negocio

**✅ Aprobaciones:**
- [ ] Product Owner aprueba el despliegue
- [ ] QA team aprueba las pruebas
- [ ] Security team aprueba la versión
- [ ] Business stakeholders notificados

**✅ Preparación:**
- [ ] Backup de producción ejecutado
- [ ] Rollback plan probado
- [ ] Equipo de soporte disponible
- [ ] Documentación actualizada

### Checklist de Seguridad

**✅ Seguridad:**
- [ ] No secrets en el código
- [ ] Dependencias actualizadas
- [ ] Vulnerabilidades escaneadas
- [ ] Permisos de base de datos verificados
- [ ] Firewall configurado

## 8. Post-Despliegue

### Monitoreo

**Herramientas de Monitoreo:**
- **Infraestructura**: Prometheus + Grafana
- **Aplicación**: New Relic / Datadog
- **Logs**: ELK Stack (Elasticsearch, Logstash, Kibana)
- **Base de Datos**: pgAdmin + custom dashboards
- **Uptime**: Pingdom / UptimeRobot

**Métricas Clave (KPIs):**

**Performance:**
- **Latencia**: < 500ms promedio
- **Throughput**: > 1000 requests/min
- **Error Rate**: < 1%
- **Uptime**: > 99.9%

**Infraestructura:**
- **CPU**: < 80% promedio
- **Memoria**: < 85% promedio
- **Disco**: < 90% usado
- **Red**: < 80% bandwidth

**Aplicación:**
- **Login Success Rate**: > 99%
- **API Response Time**: < 1s
- **Database Connections**: < 80% pool
- **Active Users**: Monitoreo en tiempo real

### Dashboard de Monitoreo

**URLs de Monitoreo:**
- **Grafana**: `https://grafana.tuempresa.com`
- **Kibana**: `https://kibana.tuempresa.com`
- **New Relic**: `https://newrelic.com/accounts/[ID]`
- **Health Check**: `https://api.tuempresa.com/health`

**Alertas Configuradas:**
```yaml
# Prometheus Alert Rules
groups:
  - name: talent-management
    rules:
      - alert: HighErrorRate
        expr: rate(http_requests_total{status=~"5.."}[5m]) > 0.05
        for: 5m
        labels:
          severity: critical
        annotations:
          summary: "Error rate is high"
          
      - alert: HighLatency
        expr: histogram_quantile(0.95, rate(http_request_duration_seconds_bucket[5m])) > 2
        for: 5m
        labels:
          severity: warning
```

### Retrospectiva

**Reunión Post-Despliegue (48h después):**
- **Participantes**: DevOps, Developers, QA, Product Owner
- **Duración**: 1 hora
- **Agenda**:
  1. Revisión de métricas post-despliegue
  2. Incidentes o problemas encontrados
  3. Lecciones aprendidas
  4. Mejoras para próximos despliegues
  5. Acciones de seguimiento

**Template de Retrospectiva:**
```
📊 RETROSPECTIVA DESPLIEGUE EvalTrack v2.0.0
Fecha: [FECHA]
Participantes: [LISTA]

✅ Qué salió bien:
- [ITEM 1]
- [ITEM 2]

❌ Qué salió mal:
- [ITEM 1]
- [ITEM 2]

🔧 Mejoras para próximos despliegues:
- [ACCION 1] - Responsable: [NOMBRE] - Fecha: [FECHA]
- [ACCION 2] - Responsable: [NOMBRE] - Fecha: [FECHA]

📈 Métricas post-despliegue:
- Uptime: [PORCENTAJE]
- Error Rate: [PORCENTAJE]
- Latencia promedio: [TIEMPO]
- Usuarios activos: [NÚMERO]
- Migración de datos: [ESTADO]
```

---

## 📋 Resumen del Plan

**Estado del Despliegue**: ✅ LISTO PARA PRODUCCIÓN
**Versión**: EvalTrack v2.0.0 (Migración desde Laravel v1.0.0)
**Fecha de Despliegue**: [FECHA]
**Ventana de Mantenimiento**: Domingo 02:00-05:00 AM (UTC-5)
**Tiempo Estimado**: 3 horas (incluye migración de datos)
**Equipo Responsable**: DevOps Team

**Próximos Pasos:**
1. Ejecutar checklist pre-despliegue
2. Confirmar aprobación de stakeholders
3. Ejecutar migración de datos desde v1.0.0
4. Ejecutar script de despliegue v2.0.0
5. Monitorear métricas post-despliegue
6. Realizar retrospectiva

**Repositorios:**
- **v1.0.0 (Legacy)**: https://github.com/DarwinValdiviezo/evaltrack.git
- **v2.0.0 (Nueva)**: [URL_DEL_NUEVO_REPO]

---
*Documento actualizado: [FECHA]*
*Versión del documento: 2.0* 