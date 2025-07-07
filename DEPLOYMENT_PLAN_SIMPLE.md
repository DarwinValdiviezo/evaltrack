# 🚀 Plan de Despliegue Simplificado - EvalTrack

## 1. Objetivo y Alcance

**Propósito:**  
Despliegue automatizado de EvalTrack v1.0.0 - Sistema de gestión de talento humano sin dependencias complejas de CI/CD.

**Ámbito:**  
- Backend Laravel 12 (API y lógica de negocio)
- Frontend Blade (vistas para usuario, gestor y administrador)
- Bases de datos híbridas:
  - PostgreSQL (usuarios, roles, permisos, sesiones)
  - MySQL (empleados, eventos, asistencias, evaluaciones)
- Configuración Docker local y scripts de automatización

**Dependencias:**  
- Docker y Docker Compose
- XAMPP (entorno local)
- Composer y NPM
- Scripts bash automatizados

---

## 2. Versiones y Artefactos

**Código/Artefacto:**  
- Nombre: `evaltrack:v1.0.0`
- Repositorio: [github.com/DarwinValdiviezo/evaltrack](https://github.com/DarwinValdiviezo/evaltrack.git)
- Rama: `main`
- Último commit: Consultar en repositorio
- Artefactos: Aplicación Laravel completa con Docker

**Configuraciones:**  
- Variables críticas: `.env` (basado en `env.example`)
- Secrets: Credenciales de BD y servicios externos
- Docker Compose para orquestación

---

## 3. Entornos y Estrategia de Despliegue

**Flujo simplificado:**  
- Desarrollo → Local (Docker/XAMPP)
- Staging → Manual con scripts
- Producción → Manual con scripts + Docker

**Estrategia:**  
- **Blue/Green simple**: Scripts de backup y rollback
- **Ventana de mantenimiento**: 30 minutos máximo
- **Rollback automático**: Si health check falla

---

## 4. Plan de Ejecución

### 4.1 Comandos Clave Automatizados

```bash
# Despliegue completo con un comando
./scripts/deploy-simple.sh production

# Solo migraciones
./scripts/migrate-only.sh

# Solo backup
./scripts/backup-only.sh

# Health check
./scripts/health-check.sh
```

### 4.2 Orden de Despliegue

1. **Backup automático** de BD y archivos
2. **Pull del código** más reciente
3. **Instalación de dependencias** (Composer + NPM)
4. **Configuración de entorno** (.env)
5. **Migraciones de BD** (PostgreSQL + MySQL)
6. **Optimización** (cache, assets)
7. **Health check** y validación
8. **Notificación** de éxito/fallo

---

## 5. Rollback y Mitigación de Riesgos

**Condiciones de fallo:**
- Health check falla (5% errores en 2 minutos)
- Migraciones fallan
- Servicios no inician en 5 minutos

**Procedimiento de rollback:**
```bash
# Rollback automático
./scripts/rollback.sh

# Restaurar backup
./scripts/restore-backup.sh [timestamp]
```

---

## 6. Equipo y Comunicación

**Responsables:**
- **DevOps/Desarrollador**: Ejecución de scripts
- **QA**: Validación post-despliegue
- **Soporte**: Monitoreo y atención de incidencias

**Notificaciones:**
- Email automático con resultado del despliegue
- Logs detallados en `storage/logs/deploy.log`
- Slack/Teams webhook (opcional)

---

## 7. Checklist Pre-Despliegue

| Item | Verificación | Comando |
|------|-------------|---------|
| Código probado | ✅ Tests pasan | `./scripts/test-local.sh` |
| Backup reciente | ✅ BD y archivos | `./scripts/backup-only.sh` |
| Variables de entorno | ✅ .env configurado | `php artisan config:cache` |
| Dependencias | ✅ Composer/NPM | `composer install && npm install` |
| Permisos | ✅ Storage y cache | `chmod -R 775 storage bootstrap/cache` |
| Servicios | ✅ Docker/BD activos | `docker-compose ps` |

---

## 8. Post-Despliegue

**Monitoreo automático:**
- Health check cada 5 minutos
- Logs en tiempo real
- Métricas básicas (CPU, memoria, errores)

**Retrospectiva:**
- Log de despliegue con duración y errores
- Métricas de rendimiento post-despliegue
- Feedback de usuarios (opcional)

---

## 9. Scripts de Automatización

### 9.1 Script Principal de Despliegue

```bash
#!/bin/bash
# deploy-simple.sh - Despliegue automatizado sin CI/CD complejo

set -e

ENVIRONMENT=${1:-local}
LOG_FILE="storage/logs/deploy-$(date +%Y%m%d_%H%M%S).log"

# Función de logging
log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

# Backup automático
log "📦 Iniciando backup..."
./scripts/backup-only.sh

# Pull del código
log "📥 Actualizando código..."
git pull origin main

# Instalar dependencias
log "📦 Instalando dependencias..."
composer install --no-dev --optimize-autoloader
npm ci --production

# Configurar entorno
log "⚙️ Configurando entorno..."
cp .env.example .env
php artisan key:generate --force

# Migraciones
log "🗄️ Ejecutando migraciones..."
php artisan migrate --force

# Optimizar
log "⚡ Optimizando aplicación..."
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Health check
log "🏥 Verificando aplicación..."
./scripts/health-check.sh

log "✅ Despliegue completado exitosamente!"
```

### 9.2 Script de Rollback

```bash
#!/bin/bash
# rollback.sh - Rollback automático en caso de fallo

set -e

log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1"
}

log "🔄 Iniciando rollback..."

# Restaurar código
git reset --hard HEAD~1

# Restaurar backup más reciente
./scripts/restore-backup.sh

# Limpiar caché
php artisan cache:clear
php artisan config:clear

log "✅ Rollback completado"
```

---

## 10. Configuración de Monitoreo Simple

### 10.1 Health Check Endpoint

```php
// routes/web.php
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now(),
        'version' => '1.0.0',
        'database' => [
            'postgresql' => DB::connection('pgsql')->getPdo() ? 'connected' : 'error',
            'mysql' => DB::connection('mysql_business')->getPdo() ? 'connected' : 'error'
        ]
    ]);
});
```

### 10.2 Script de Monitoreo

```bash
#!/bin/bash
# monitor.sh - Monitoreo básico de la aplicación

while true; do
    if curl -f http://localhost/health > /dev/null 2>&1; then
        echo "[$(date)] ✅ Aplicación funcionando"
    else
        echo "[$(date)] ❌ Aplicación caída - enviando alerta"
        # Enviar email/Slack de alerta
    fi
    sleep 300  # 5 minutos
done
```

---

## 11. Ventajas de esta Solución

✅ **Sin dependencias complejas**: No requiere GitHub Actions, Jenkins, etc.
✅ **Fácil de entender**: Scripts bash simples y claros
✅ **Rápido de implementar**: 30 minutos de configuración
✅ **Confiable**: Backup automático y rollback
✅ **Escalable**: Fácil de adaptar a diferentes entornos
✅ **Monitoreo básico**: Health checks y logs automáticos

---

## 12. Próximos Pasos

1. **Implementar scripts** de automatización
2. **Configurar monitoreo** básico
3. **Probar en staging** con datos reales
4. **Documentar procedimientos** específicos del equipo
5. **Capacitar al equipo** en el uso de scripts

---

**Estado**: ✅ Listo para implementación
**Tiempo estimado**: 2-3 horas de configuración
**Complejidad**: Baja (sin CI/CD complejo) 