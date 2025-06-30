# Plan de Despliegue DevOps - EvalTrack v1.0.0

## 1. Objetivo y Alcance

### Propósito
Lanzamiento de la versión 1.0.0 del Sistema de Gestión de Talento Humano "EvalTrack" - Plataforma web para la gestión integral de empleados, eventos, asistencias y evaluaciones corporativas.

### Ámbito
**Componentes a desplegar:**
- Aplicación web Laravel 12.0 (PHP 8.2+)
- Base de datos PostgreSQL (usuarios y roles)
- Base de datos MySQL (datos de negocio)
- Servidor web Nginx
- Redis para cache y sesiones
- Sistema de archivos para logs y storage

**Arquitectura:**
```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Load Balancer │───▶│  Web Servers    │───▶│  PostgreSQL DB  │
│   (Nginx)       │    │  (PHP-FPM)      │    │  (Users/Roles)  │
└─────────────────┘    └─────────────────┘    └─────────────────┘
                              │
                              ▼
                       ┌─────────────────┐    ┌─────────────────┐
                       │  MySQL Database │    │  Redis Cache    │
                       │  (Business Data)│    │  (Sessions)     │
                       └─────────────────┘    └─────────────────┘
```

### Dependencias
**Servicios Externos:**
- GitHub/GitLab (repositorio de código)
- Docker Hub (registro de imágenes)
- SSL Certificate Authority (Let's Encrypt)

**Servicios Internos:**
- Base de datos PostgreSQL 15+
- Base de datos MySQL 8.0+
- Redis 7.0+
- Nginx 1.24+
- PHP-FPM 8.2+

## 2. Versiones y Artefactos

### Código/Artefacto
- **Nombre del artefacto:** `evaltrack-web:v1.0.0`
- **Repositorio:** `https://github.com/company/evaltrack`
- **Commit/Tag:** `v1.0.0` (commit: `a1b2c3d4e5f6`)
- **Registro Docker:** `docker.io/company/evaltrack:1.0.0`

### Configuraciones
**Variables de entorno críticas (`prod.env`):**
```env
# Aplicación
APP_NAME=EvalTrack
APP_ENV=production
APP_KEY=base64:your-app-key-here
APP_DEBUG=false
APP_URL=https://evaltrack.company.com
APP_TIMEZONE=America/Caracas

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

# Redis
REDIS_HOST=redis-prod.company.com
REDIS_PASSWORD=redis_password_here
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.company.com
MAIL_PORT=587
MAIL_USERNAME=noreply@company.com
MAIL_PASSWORD=mail_password_here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@company.com
MAIL_FROM_NAME="EvalTrack System"

# Logs
LOG_CHANNEL=stack
LOG_LEVEL=error
```

**Secrets (Kubernetes Secrets / HashiCorp Vault):**
- `evaltrack-postgres-password`
- `evaltrack-mysql-password`
- `evaltrack-redis-password`
- `evaltrack-app-key`
- `evaltrack-mail-password`
- `evaltrack-ssl-cert`
- `evaltrack-ssl-key`

## 3. Entornos y Pipeline CI/CD

### Flujo de Despliegue
```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   Develop   │───▶│     QA      │───▶│   Staging   │───▶│ Production  │
│   (GitHub)  │    │  (Testing)  │    │ (Pre-prod)  │    │   (Live)    │
└─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘
```

**Etapas del Pipeline:**
1. **Develop** → Build automático en cada push
2. **QA** → Despliegue automático tras merge a `develop`
3. **Staging** → Despliegue manual tras aprobación QA
4. **Production** → Despliegue manual tras aprobación Staging

**Enlace al Pipeline:** `https://github.com/company/evaltrack/actions`

### Estrategia de Despliegue
**Blue/Green Deployment:**
- **Blue Environment:** Versión actual en producción
- **Green Environment:** Nueva versión a desplegar
- **Switch:** Cambio de tráfico instantáneo tras validación

**Ventana de Mantenimiento:**
- **Día:** Domingo
- **Hora:** 02:00 - 04:00 AM (GMT-4)
- **Duración estimada:** 30 minutos
- **Rollback plan:** 5 minutos máximo

## 4. Configuración de Infraestructura

### Docker Compose (Desarrollo)
```yaml
version: '3.8'
services:
  app:
    build: .
    ports:
      - "8000:80"
    environment:
      - APP_ENV=local
    volumes:
      - ./storage:/var/www/html/storage
    depends_on:
      - postgres
      - mysql
      - redis

  postgres:
    image: postgres:15
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
```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: evaltrack-web
  namespace: evaltrack
spec:
  replicas: 3
  selector:
    matchLabels:
      app: evaltrack-web
  template:
    metadata:
      labels:
        app: evaltrack-web
    spec:
      containers:
      - name: evaltrack
        image: company/evaltrack:1.0.0
        ports:
        - containerPort: 80
        env:
        - name: APP_ENV
          value: "production"
        - name: DB_PGSQL_PASSWORD
          valueFrom:
            secretKeyRef:
              name: evaltrack-secrets
              key: postgres-password
        resources:
          requests:
            memory: "256Mi"
            cpu: "250m"
          limits:
            memory: "512Mi"
            cpu: "500m"
```

## 5. Monitoreo y Logging

### Métricas a Monitorear
- **Aplicación:** Response time, error rate, throughput
- **Base de datos:** Connection pool, query performance
- **Infraestructura:** CPU, memoria, disco, red
- **Negocio:** Usuarios activos, eventos creados, evaluaciones completadas

### Herramientas
- **APM:** New Relic / DataDog
- **Logs:** ELK Stack (Elasticsearch, Logstash, Kibana)
- **Métricas:** Prometheus + Grafana
- **Alertas:** PagerDuty / Slack

## 6. Plan de Rollback

### Criterios de Rollback
- Error rate > 5% por 5 minutos
- Response time > 2 segundos promedio
- Errores críticos en logs
- Fallo en health checks

### Procedimiento de Rollback
1. Detener despliegue de nueva versión
2. Cambiar tráfico a versión anterior (Blue)
3. Verificar métricas y logs
4. Investigar causa del problema
5. Corregir y re-desplegar

## 7. Checklist de Despliegue

### Pre-despliegue
- [ ] Tests unitarios pasando (cobertura > 80%)
- [ ] Tests de integración pasando
- [ ] Code review aprobado
- [ ] Documentación actualizada
- [ ] Backup de base de datos
- [ ] Notificación a stakeholders

### Durante el despliegue
- [ ] Desplegar en Staging
- [ ] Validar funcionalidades críticas
- [ ] Verificar métricas de rendimiento
- [ ] Aprobar despliegue a producción
- [ ] Ejecutar migraciones de base de datos
- [ ] Verificar health checks

### Post-despliegue
- [ ] Monitorear métricas por 30 minutos
- [ ] Verificar logs de errores
- [ ] Validar funcionalidades críticas
- [ ] Notificar éxito del despliegue
- [ ] Actualizar documentación de versión

## 8. Contactos y Responsabilidades

### Equipo de Despliegue
- **DevOps Lead:** [Nombre] - [Email] - [Teléfono]
- **Backend Lead:** [Nombre] - [Email] - [Teléfono]
- **QA Lead:** [Nombre] - [Email] - [Teléfono]
- **Product Owner:** [Nombre] - [Email] - [Teléfono]

### Escalación
1. **Nivel 1:** Equipo de desarrollo (15 min)
2. **Nivel 2:** DevOps Lead (30 min)
3. **Nivel 3:** CTO (1 hora)

---

**Documento creado:** [Fecha]
**Última actualización:** [Fecha]
**Versión del documento:** 1.0 