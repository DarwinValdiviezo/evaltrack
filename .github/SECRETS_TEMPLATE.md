# Secrets Requeridos para GitHub Actions

## 🔐 Secrets del Repositorio

Configura estos secrets en tu repositorio de GitHub:
`Settings` → `Secrets and variables` → `Actions`

### Docker Hub
```
DOCKERHUB_USERNAME=tu_usuario_dockerhub
DOCKERHUB_TOKEN=tu_token_dockerhub
```

### Staging Environment
```
STAGING_HOST=staging.evaltrack.com
STAGING_USER=deploy
STAGING_SSH_KEY=-----BEGIN OPENSSH PRIVATE KEY-----
tu_clave_privada_ssh_staging
-----END OPENSSH PRIVATE KEY-----
```

### Production Environment
```
PROD_HOST=evaltrack.com
PROD_USER=deploy
PROD_SSH_KEY=-----BEGIN OPENSSH PRIVATE KEY-----
tu_clave_privada_ssh_produccion
-----END OPENSSH PRIVATE KEY-----
```

### Database
```
DB_PASSWORD=tu_password_postgres_produccion
JWT_SECRET=tu_jwt_secret_super_seguro_produccion
```

### Monitoring
```
GRAFANA_PASSWORD=tu_password_grafana
```

### Notifications
```
SLACK_WEBHOOK_URL=https://hooks.slack.com/services/T00000000/B00000000/XXXXXXXXXXXXXXXXXXXXXXXX
```

## 🔧 Variables de Entorno

### Staging (.env)
```env
NODE_ENV=staging
DATABASE_URL=postgresql://evaltrack_user:password@staging-db:5432/evaltrack_staging
JWT_SECRET=staging-jwt-secret
JWT_EXPIRES_IN=24h
REDIS_URL=redis://staging-redis:6379
LOG_LEVEL=debug
FRONTEND_API_URL=https://staging.evaltrack.com/api
```

### Production (.env)
```env
NODE_ENV=production
DATABASE_URL=postgresql://evaltrack_user:password@prod-db:5432/evaltrack_prod
JWT_SECRET=production-jwt-secret-super-seguro
JWT_EXPIRES_IN=24h
REDIS_URL=redis://prod-redis:6379
LOG_LEVEL=info
FRONTEND_API_URL=https://evaltrack.com/api
```

## 📋 Checklist de Configuración

- [ ] Docker Hub credentials configurados
- [ ] SSH keys para staging y producción
- [ ] Variables de entorno configuradas
- [ ] Slack webhook configurado
- [ ] Secrets de base de datos configurados
- [ ] JWT secrets configurados
- [ ] Passwords de monitoreo configurados

## 🚨 Seguridad

⚠️ **IMPORTANTE**: 
- Nunca commits secrets en el código
- Rota los secrets regularmente
- Usa diferentes secrets para cada ambiente
- Monitorea el acceso a los secrets 