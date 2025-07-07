# 🐳 Plan de Despliegue Docker - EvalTrack

## 1. Objetivo y Alcance

**Propósito:**  
Despliegue ultra-rápido de EvalTrack v1.0.0 usando Docker - Sistema de gestión de talento humano completamente containerizado.

**Ámbito:**  
- **Aplicación Laravel 12** containerizada con PHP-FPM + Nginx
- **Bases de datos híbridas**:
  - PostgreSQL 15 (usuarios, roles, permisos)
  - MySQL 8.0 (empleados, eventos, asistencias, evaluaciones)
- **Servicios auxiliares**:
  - Redis 7 (cache y sesiones)
  - MailHog (testing de emails)
  - Adminer (gestión de BD)

**Dependencias:**  
- Docker y Docker Compose
- Git (para actualizar código)
- 4GB RAM mínimo recomendado

---

## 2. Versiones y Artefactos

**Código/Artefacto:**  
- Nombre: `evaltrack:v1.0.0`
- Repositorio: [github.com/DarwinValdiviezo/evaltrack](https://github.com/DarwinValdiviezo/evaltrack.git)
- Imagen Docker: `evaltrack-app:latest`
- Rama: `main`

**Configuraciones:**  
- `docker-compose.yml` - Orquestación de servicios
- `Dockerfile` - Imagen de la aplicación
- Variables de entorno integradas en docker-compose

---

## 3. Entornos y Estrategia de Despliegue

**Flujo simplificado:**  
- Desarrollo → Docker local (puerto 8000)
- Staging → Docker en servidor de pruebas
- Producción → Docker en servidor de producción

**Estrategia:**  
- **Blue/Green simple**: Backup de volúmenes + rollback
- **Tiempo de despliegue**: 3-5 minutos
- **Rollback automático**: Si health check falla

---

## 4. Plan de Ejecución

### 4.1 Comandos Clave

```bash
# Despliegue ultra-rápido (recomendado)
./scripts/docker-quick.sh

# Despliegue completo con backup
./scripts/deploy-docker.sh production

# Solo levantar servicios
docker-compose up -d

# Solo migraciones
docker-compose exec app php artisan migrate --force
```

### 4.2 Orden de Despliegue

1. **Verificar Docker** - Comprobar instalación y estado
2. **Backup automático** - Volúmenes de BD y archivos
3. **Pull del código** - Actualizar desde repositorio
4. **Construir imagen** - Docker build optimizado
5. **Levantar servicios** - docker-compose up
6. **Migraciones** - Ejecutar automáticamente
7. **Seeders** - Datos de prueba
8. **Optimización** - Cache y assets
9. **Health check** - Verificar funcionamiento

---

## 5. Rollback y Mitigación de Riesgos

**Condiciones de fallo:**
- Health check falla (3 intentos)
- Migraciones fallan
- Servicios no inician en 2 minutos

**Procedimiento de rollback:**
```bash
# Rollback automático
docker-compose down
docker volume restore [backup_timestamp]
docker-compose up -d
```

---

## 6. Equipo y Comunicación

**Responsables:**
- **DevOps/Desarrollador**: Ejecución de scripts Docker
- **QA**: Validación post-despliegue
- **Soporte**: Monitoreo de contenedores

**Notificaciones:**
- Logs en tiempo real: `docker-compose logs -f`
- Health check automático cada 30s

---

## 7. Checklist Pre-Despliegue

| Item | Verificación | Comando |
|------|-------------|---------|
| Docker instalado | ✅ Docker y Docker Compose | `docker --version` |
| Código actualizado | ✅ Último commit | `git pull origin main` |
| Puertos libres | ✅ 8000, 5432, 3306, 6379 | `netstat -tulpn` |
| Recursos disponibles | ✅ 4GB RAM, 10GB disco | `free -h && df -h` |
| Docker daemon | ✅ Ejecutándose | `docker info` |

---

## 8. Post-Despliegue

**Monitoreo automático:**
- Health check: http://localhost:8000/health
- Logs en tiempo real: `docker-compose logs -f app`
- Métricas de contenedores: `docker stats`

**Validaciones:**
- Aplicación responde en puerto 8000
- Bases de datos conectadas
- Emails funcionando (MailHog)
- Adminer accesible

---

## 9. Scripts de Automatización

### 9.1 Script Ultra-Rápido

```bash
#!/bin/bash
# docker-quick.sh - Despliegue en 3 minutos

set -e

log() { echo "[$(date)] $1"; }

log "🚀 Levantando EvalTrack con Docker..."

# Verificar Docker
docker --version > /dev/null || exit 1

# Detener y limpiar
docker-compose down --remove-orphans
docker system prune -f

# Construir y levantar
docker-compose up -d --build

# Esperar y configurar
sleep 30
docker-compose exec -T app php artisan migrate --force
docker-compose exec -T app php artisan db:seed --force
docker-compose exec -T app php artisan config:cache

log "🎉 ¡Listo! http://localhost:8000"
```

### 9.2 Script de Monitoreo

```bash
#!/bin/bash
# monitor-docker.sh

while true; do
    if curl -f http://localhost:8000/health > /dev/null 2>&1; then
        echo "[$(date)] ✅ Aplicación funcionando"
    else
        echo "[$(date)] ❌ Aplicación caída"
        docker-compose restart app
    fi
    sleep 60
done
```

---

## 10. Configuración de Servicios

### 10.1 Puertos y Accesos

| Servicio | Puerto | URL | Propósito |
|----------|--------|-----|-----------|
| Aplicación | 8000 | http://localhost:8000 | EvalTrack principal |
| PostgreSQL | 5432 | - | Base de datos usuarios |
| MySQL | 3306 | - | Base de datos negocio |
| Redis | 6379 | - | Cache y sesiones |
| MailHog | 8025 | http://localhost:8025 | Testing emails |
| Adminer | 8080 | http://localhost:8080 | Gestión BD |

### 10.2 Variables de Entorno Críticas

```yaml
# docker-compose.yml
environment:
  - APP_ENV=local
  - APP_DEBUG=true
  - APP_URL=http://localhost:8000
  - DB_PGSQL_HOST=postgres
  - DB_HOST=mysql
  - REDIS_HOST=redis
  - MAIL_HOST=mailhog
```

---

## 11. Ventajas de esta Solución Docker

✅ **Ultra-rápido**: Despliegue en 3-5 minutos
✅ **Aislado**: Cada servicio en su contenedor
✅ **Portable**: Funciona igual en cualquier máquina
✅ **Escalable**: Fácil agregar más instancias
✅ **Consistente**: Mismo entorno en dev/prod
✅ **Backup automático**: Volúmenes Docker
✅ **Health checks**: Monitoreo automático
✅ **Rollback fácil**: Restaurar volúmenes

---

## 12. Comandos de Mantenimiento

```bash
# Ver logs en tiempo real
docker-compose logs -f app

# Reiniciar solo la aplicación
docker-compose restart app

# Ver estado de servicios
docker-compose ps

# Backup manual
docker run --rm -v evaltrack_postgres_data:/data -v $(pwd)/backup:/backup alpine tar czf /backup/postgres_$(date +%Y%m%d_%H%M%S).tar.gz -C /data .

# Limpiar todo
docker-compose down -v
docker system prune -a -f

# Actualizar código
git pull origin main
docker-compose up -d --build
```

---

## 13. Troubleshooting

### Problemas Comunes

| Problema | Solución |
|----------|----------|
| Puerto 8000 ocupado | `docker-compose down && docker-compose up -d` |
| Migraciones fallan | `docker-compose exec app php artisan migrate:fresh --seed` |
| Aplicación no responde | `docker-compose restart app` |
| Base de datos no conecta | `docker-compose restart postgres mysql` |
| Permisos de archivos | `docker-compose exec app chown -R www-data:www-data storage` |

### Logs de Debug

```bash
# Ver logs de todos los servicios
docker-compose logs

# Ver logs de un servicio específico
docker-compose logs app
docker-compose logs postgres
docker-compose logs mysql

# Ver logs en tiempo real
docker-compose logs -f
```

---

## 14. Próximos Pasos

1. **Ejecutar script rápido**: `./scripts/docker-quick.sh`
2. **Verificar acceso**: http://localhost:8000
3. **Probar funcionalidades**: Login, CRUD, emails
4. **Configurar monitoreo**: Script de health check
5. **Documentar procedimientos**: Para el equipo

---

**Estado**: ✅ Listo para ejecución
**Tiempo estimado**: 3-5 minutos
**Complejidad**: Baja (Docker automatizado)
**Requisitos**: Docker + Docker Compose 