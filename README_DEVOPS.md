# DevOps Documentation - EvalTrack

## 📋 Tabla de Contenidos

1. [Descripción General](#descripción-general)
2. [Arquitectura de Infraestructura](#arquitectura-de-infraestructura)
3. [Configuración de Entornos](#configuración-de-entornos)
4. [Pipeline CI/CD](#pipeline-cicd)
5. [Despliegue](#despliegue)
6. [Monitoreo y Logging](#monitoreo-y-logging)
7. [Seguridad](#seguridad)
8. [Backup y Recuperación](#backup-y-recuperación)
9. [Troubleshooting](#troubleshooting)

## 🎯 Descripción General

EvalTrack es un sistema de gestión de talento humano desarrollado en Laravel 12.0 con arquitectura de microservicios y despliegue en Kubernetes.

### Tecnologías Principales
- **Backend**: Laravel 12.0 (PHP 8.2+)
- **Frontend**: Bootstrap 4.6.2 + SB Admin 2
- **Base de Datos**: PostgreSQL 15 + MySQL 8.0
- **Cache**: Redis 7.0
- **Contenedores**: Docker
- **Orquestación**: Kubernetes
- **CI/CD**: GitHub Actions
- **Monitoreo**: Prometheus + Grafana

## 🏗️ Arquitectura de Infraestructura

### Diagrama de Arquitectura
```
┌─────────────────────────────────────────────────────────────┐
│                    Load Balancer (Nginx)                    │
└─────────────────────┬───────────────────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────────────────┐
│                    Kubernetes Cluster                       │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────┐  │
│  │   EvalTrack     │  │   PostgreSQL    │  │    Redis    │  │
│  │   (3 replicas)  │  │   (Primary)     │  │   (Cache)   │  │
│  └─────────────────┘  └─────────────────┘  └─────────────┘  │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────┐  │
│  │   MySQL         │  │   Monitoring    │  │   Logging   │  │
│  │   (Business)    │  │   (Prometheus)  │  │   (ELK)     │  │
│  └─────────────────┘  └─────────────────┘  └─────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

### Componentes de Infraestructura

#### Aplicación Principal
- **Imagen**: `company/evaltrack:1.0.0`
- **Réplicas**: 3 (producción), 1 (desarrollo/staging)
- **Recursos**: 256Mi-512Mi RAM, 250m-500m CPU
- **Health Check**: `/health`

#### Base de Datos
- **PostgreSQL**: Usuarios y roles (Spatie Permission)
- **MySQL**: Datos de negocio (empleados, eventos, asistencias, evaluaciones)
- **Redis**: Cache y sesiones

#### Monitoreo
- **Prometheus**: Métricas de aplicación y infraestructura
- **Grafana**: Dashboards y alertas
- **ELK Stack**: Logs centralizados

## 🌍 Configuración de Entornos

### Variables de Entorno por Entorno

#### Desarrollo
```env
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=pgsql
DB_PGSQL_HOST=postgres
DB_PGSQL_DATABASE=evaltrack_users
DB_HOST=mysql
DB_DATABASE=evaltrack_business
REDIS_HOST=redis
```

#### Staging
```env
APP_ENV=staging
APP_DEBUG=false
DB_PGSQL_HOST=postgres-staging.company.com
DB_HOST=mysql-staging.company.com
REDIS_HOST=redis-staging.company.com
```

#### Producción
```env
APP_ENV=production
APP_DEBUG=false
DB_PGSQL_HOST=postgres-prod.company.com
DB_HOST=mysql-prod.company.com
REDIS_HOST=redis-prod.company.com
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### Secrets de Kubernetes
```yaml
apiVersion: v1
kind: Secret
metadata:
  name: evaltrack-secrets
type: Opaque
data:
  postgres-password: <base64-encoded>
  mysql-password: <base64-encoded>
  redis-password: <base64-encoded>
  mail-password: <base64-encoded>
  app-key: <base64-encoded>
```

## 🔄 Pipeline CI/CD

### Flujo del Pipeline
```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   Push to   │───▶│   Tests &    │───▶│   Build &    │───▶│   Deploy to  │
│   GitHub    │    │   Analysis   │    │   Push Image │    │   Environment│
└─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘
```

### Etapas del Pipeline

#### 1. Tests y Análisis
- Tests unitarios (PHPUnit)
- Tests de integración
- Análisis estático (PHPStan)
- Verificación de estilo (Laravel Pint)
- Análisis de seguridad (Enlightn)
- Cobertura de código (>80%)

#### 2. Construcción de Imagen
- Multi-stage Docker build
- Optimización para producción
- Push a Docker Hub
- Cache de capas

#### 3. Despliegue
- **Desarrollo**: Despliegue automático
- **Staging**: Despliegue manual tras aprobación
- **Producción**: Blue/Green deployment

### Configuración de GitHub Actions
```yaml
# .github/workflows/ci-cd.yml
name: CI/CD Pipeline - EvalTrack
on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main, develop ]
```

## 🚀 Despliegue

### Estrategias de Despliegue

#### Blue/Green (Producción)
1. Desplegar nueva versión en Green environment
2. Health check en Green
3. Cambiar tráfico a Green
4. Verificar métricas
5. Escalar Blue a 0 réplicas

#### Rolling Update (Desarrollo/Staging)
1. Actualizar imagen en deployment
2. Kubernetes maneja el rollout
3. Health check automático
4. Rollback automático si falla

### Comandos de Despliegue

#### Despliegue Manual
```bash
# Despliegue a desarrollo
./scripts/deploy.sh development 1.0.0

# Despliegue a staging
./scripts/deploy.sh staging 1.0.0

# Despliegue a producción
./scripts/deploy.sh production 1.0.0
```

#### Despliegue con kubectl
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
docker-compose exec app php artisan migrate

# Ejecutar seeders
docker-compose exec app php artisan db:seed
```

## 📊 Monitoreo y Logging

### Métricas a Monitorear

#### Aplicación
- Response time promedio
- Error rate
- Throughput (requests/segundo)
- Uso de memoria y CPU
- Conexiones de base de datos

#### Infraestructura
- CPU, memoria, disco
- Latencia de red
- Disponibilidad de servicios
- Uso de recursos Kubernetes

#### Negocio
- Usuarios activos
- Eventos creados
- Asistencias registradas
- Evaluaciones completadas

### Configuración de Prometheus
```yaml
# prometheus.yml
global:
  scrape_interval: 15s

scrape_configs:
  - job_name: 'evaltrack'
    static_configs:
      - targets: ['evaltrack-web-service:80']
    metrics_path: '/metrics'
    scrape_interval: 30s
```

### Dashboards de Grafana
- **EvalTrack Overview**: Métricas generales
- **Application Performance**: Rendimiento de la aplicación
- **Database Performance**: Rendimiento de base de datos
- **Infrastructure**: Métricas de infraestructura

### Logging con ELK Stack
```yaml
# logstash.conf
input {
  beats {
    port => 5044
  }
}

filter {
  if [fields][service] == "evaltrack" {
    grok {
      match => { "message" => "%{TIMESTAMP_ISO8601:timestamp} %{LOGLEVEL:level} %{GREEDYDATA:message}" }
    }
  }
}

output {
  elasticsearch {
    hosts => ["elasticsearch:9200"]
    index => "evaltrack-%{+YYYY.MM.dd}"
  }
}
```

## 🔒 Seguridad

### Configuraciones de Seguridad

#### Kubernetes
- Pod Security Policies
- Network Policies
- RBAC (Role-Based Access Control)
- Secrets management

#### Aplicación
- HTTPS obligatorio
- Headers de seguridad
- Rate limiting
- Validación de entrada
- Sanitización de datos

#### Base de Datos
- Conexiones SSL
- Usuarios con permisos mínimos
- Backup encriptado
- Auditoría de accesos

### Configuración de Nginx
```nginx
# Headers de seguridad
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header X-Content-Type-Options "nosniff" always;
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

# Rate limiting
limit_req_zone $binary_remote_addr zone=api:10m rate=10r/s;
limit_req_zone $binary_remote_addr zone=login:10m rate=1r/s;
```

## 💾 Backup y Recuperación

### Estrategia de Backup

#### Base de Datos
- **PostgreSQL**: Backup diario completo + WAL archiving
- **MySQL**: Backup diario completo + binlog
- **Retención**: 30 días

#### Archivos
- **Storage**: Backup diario de `/var/www/html/storage`
- **Logs**: Backup semanal de logs
- **Configuración**: Backup de configs de Kubernetes

### Scripts de Backup
```bash
#!/bin/bash
# backup.sh

# Backup PostgreSQL
pg_dump -h $DB_PGSQL_HOST -U $DB_PGSQL_USERNAME $DB_PGSQL_DATABASE > postgres_backup_$(date +%Y%m%d).sql

# Backup MySQL
mysqldump -h $DB_HOST -u $DB_USERNAME -p$DB_PASSWORD $DB_DATABASE > mysql_backup_$(date +%Y%m%d).sql

# Compresión
gzip postgres_backup_$(date +%Y%m%d).sql
gzip mysql_backup_$(date +%Y%m%d).sql

# Upload a S3
aws s3 cp postgres_backup_$(date +%Y%m%d).sql.gz s3://evaltrack-backups/
aws s3 cp mysql_backup_$(date +%Y%m%d).sql.gz s3://evaltrack-backups/
```

### Recuperación de Desastres
```bash
#!/bin/bash
# restore.sh

# Descargar backup
aws s3 cp s3://evaltrack-backups/postgres_backup_20241201.sql.gz .
aws s3 cp s3://evaltrack-backups/mysql_backup_20241201.sql.gz .

# Restaurar PostgreSQL
gunzip postgres_backup_20241201.sql.gz
psql -h $DB_PGSQL_HOST -U $DB_PGSQL_USERNAME $DB_PGSQL_DATABASE < postgres_backup_20241201.sql

# Restaurar MySQL
gunzip mysql_backup_20241201.sql.gz
mysql -h $DB_HOST -u $DB_USERNAME -p$DB_PASSWORD $DB_DATABASE < mysql_backup_20241201.sql
```

## 🔧 Troubleshooting

### Problemas Comunes

#### Aplicación no responde
```bash
# Verificar pods
kubectl get pods -n evaltrack-prod

# Verificar logs
kubectl logs -f deployment/evaltrack-web -n evaltrack-prod

# Verificar health check
curl -f https://evaltrack.company.com/health
```

#### Problemas de base de datos
```bash
# Verificar conectividad PostgreSQL
kubectl exec -it deployment/postgres -n evaltrack-prod -- psql -U evaltrack_user -d evaltrack_users

# Verificar conectividad MySQL
kubectl exec -it deployment/mysql -n evaltrack-prod -- mysql -u evaltrack_user -p

# Verificar logs de base de datos
kubectl logs -f deployment/postgres -n evaltrack-prod
kubectl logs -f deployment/mysql -n evaltrack-prod
```

#### Problemas de memoria
```bash
# Verificar uso de recursos
kubectl top pods -n evaltrack-prod

# Verificar métricas de Prometheus
curl -s http://prometheus:9090/api/v1/query?query=container_memory_usage_bytes

# Analizar heap dump
kubectl exec -it deployment/evaltrack-web -n evaltrack-prod -- php artisan tinker
```

### Comandos Útiles

#### Kubernetes
```bash
# Verificar estado del cluster
kubectl cluster-info

# Verificar namespaces
kubectl get namespaces

# Verificar deployments
kubectl get deployments -n evaltrack-prod

# Verificar servicios
kubectl get services -n evaltrack-prod

# Verificar ingress
kubectl get ingress -n evaltrack-prod
```

#### Docker
```bash
# Verificar imágenes
docker images | grep evaltrack

# Verificar contenedores
docker ps -a | grep evaltrack

# Verificar logs
docker logs evaltrack-app

# Ejecutar shell en contenedor
docker exec -it evaltrack-app /bin/sh
```

#### Laravel
```bash
# Verificar configuración
php artisan config:show

# Limpiar cache
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Verificar logs
tail -f storage/logs/laravel.log

# Verificar permisos
ls -la storage/
ls -la bootstrap/cache/
```

## 📞 Contactos y Soporte

### Equipo de DevOps
- **DevOps Lead**: [Nombre] - [Email] - [Teléfono]
- **Infrastructure Engineer**: [Nombre] - [Email] - [Teléfono]
- **Security Engineer**: [Nombre] - [Email] - [Teléfono]

### Escalación
1. **Nivel 1**: Equipo de desarrollo (15 min)
2. **Nivel 2**: DevOps Lead (30 min)
3. **Nivel 3**: CTO (1 hora)

### Canales de Comunicación
- **Slack**: #evaltrack-devops
- **Email**: devops@company.com
- **PagerDuty**: EvalTrack DevOps

---

**Documento creado**: Diciembre 2024
**Última actualización**: Diciembre 2024
**Versión**: 1.0.0 