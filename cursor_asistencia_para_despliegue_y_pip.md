# Guía de Asistencia para Despliegue y Gestión de Dependencias

## 📋 Índice
1. [Gestión de Dependencias](#gestión-de-dependencias)
2. [Configuración del Entorno](#configuración-del-entorno)
3. [Despliegue del Backend](#despliegue-del-backend)
4. [Despliegue del Frontend](#despliegue-del-frontend)
5. [Variables de Entorno](#variables-de-entorno)
6. [Base de Datos](#base-de-datos)
7. [Monitoreo y Logs](#monitoreo-y-logs)

---

## 🛠️ Gestión de Dependencias

### Backend (NestJS)
```bash
# Instalar dependencias
cd backend
npm install

# Instalar dependencias de desarrollo
npm install --save-dev

# Actualizar dependencias
npm update

# Verificar vulnerabilidades
npm audit
npm audit fix

# Limpiar cache
npm cache clean --force
```

### Frontend (cuando esté configurado)
```bash
# Instalar dependencias
cd frontend
npm install

# Para proyectos React/Vue/Angular
npm run build
```

---

## ⚙️ Configuración del Entorno

### Variables de Entorno (.env)
Crear archivo `.env` en el directorio `backend/`:

```env
# Base de datos
DATABASE_URL="postgresql://usuario:contraseña@localhost:5432/nombre_db"

# JWT
JWT_SECRET="tu_jwt_secret_super_seguro"
JWT_EXPIRES_IN="24h"

# Servidor
PORT=3000
NODE_ENV=production

# CORS
CORS_ORIGIN="https://tu-dominio.com"

# Logs
LOG_LEVEL="info"
```

### Configuración de Prisma
```bash
# Generar cliente Prisma
cd backend
npx prisma generate

# Ejecutar migraciones
npx prisma migrate deploy

# Ver estado de la base de datos
npx prisma db push
```

---

## 🚀 Despliegue del Backend

### Opción 1: Despliegue Local
```bash
# Construir para producción
cd backend
npm run build

# Ejecutar en producción
npm run start:prod
```

### Opción 2: Docker
Crear `Dockerfile` en el directorio `backend/`:

```dockerfile
FROM node:18-alpine

WORKDIR /app

COPY package*.json ./
RUN npm ci --only=production

COPY . .
RUN npm run build

EXPOSE 3000

CMD ["npm", "run", "start:prod"]
```

```bash
# Construir imagen
docker build -t mi-backend .

# Ejecutar contenedor
docker run -p 3000:3000 --env-file .env mi-backend
```

### Opción 3: Despliegue en la Nube

#### Heroku
```bash
# Instalar Heroku CLI
# Crear app en Heroku
heroku create mi-backend-app

# Configurar variables de entorno
heroku config:set DATABASE_URL="tu_url_de_base_de_datos"
heroku config:set JWT_SECRET="tu_secret"

# Desplegar
git push heroku main
```

#### Railway
```bash
# Instalar Railway CLI
npm install -g @railway/cli

# Login y desplegar
railway login
railway init
railway up
```

#### Vercel
```bash
# Instalar Vercel CLI
npm install -g vercel

# Desplegar
vercel --prod
```

---

## 🎨 Despliegue del Frontend

### Configuración Básica
Cuando tengas tu frontend configurado:

```bash
# Construir para producción
cd frontend
npm run build

# Servir archivos estáticos
npm install -g serve
serve -s build -l 3001
```

### Despliegue en Vercel/Netlify
```bash
# Vercel
vercel --prod

# Netlify
netlify deploy --prod
```

---

## 🔧 Variables de Entorno

### Desarrollo
```bash
# Crear .env.development
NODE_ENV=development
DATABASE_URL="postgresql://localhost:5432/dev_db"
PORT=3000
```

### Producción
```bash
# Crear .env.production
NODE_ENV=production
DATABASE_URL="postgresql://prod_user:prod_pass@prod_host:5432/prod_db"
PORT=3000
JWT_SECRET="secret_produccion_muy_seguro"
```

---

## 🗄️ Base de Datos

### PostgreSQL (Recomendado)
```bash
# Instalar PostgreSQL
# Ubuntu/Debian
sudo apt-get install postgresql postgresql-contrib

# Windows
# Descargar desde https://www.postgresql.org/download/windows/

# Crear base de datos
createdb mi_proyecto_db

# Ejecutar migraciones
cd backend
npx prisma migrate deploy
```

### Configuración de Prisma
```prisma
// prisma/schema.prisma
generator client {
  provider = "prisma-client-js"
}

datasource db {
  provider = "postgresql"
  url      = env("DATABASE_URL")
}

// Definir tus modelos aquí
```

---

## 📊 Monitoreo y Logs

### Configuración de Logs
```typescript
// src/main.ts
import { Logger } from '@nestjs/common';

async function bootstrap() {
  const app = await NestFactory.create(AppModule);
  
  // Configurar logs
  const logger = new Logger('Bootstrap');
  logger.log('Aplicación iniciada en puerto 3000');
  
  await app.listen(3000);
}
```

### Health Checks
```typescript
// src/health/health.controller.ts
import { Controller, Get } from '@nestjs/common';

@Controller('health')
export class HealthController {
  @Get()
  check() {
    return {
      status: 'ok',
      timestamp: new Date().toISOString(),
      uptime: process.uptime()
    };
  }
}
```

---

## 🔍 Comandos Útiles

### Desarrollo
```bash
# Ejecutar en modo desarrollo
npm run start:dev

# Ejecutar tests
npm run test
npm run test:e2e

# Linting
npm run lint
npm run format
```

### Producción
```bash
# Verificar estado de la aplicación
curl http://localhost:3000/health

# Ver logs
docker logs -f container_name

# Reiniciar servicios
docker-compose restart
```

---

## 🚨 Troubleshooting

### Problemas Comunes

1. **Error de conexión a base de datos**
   ```bash
   # Verificar conexión
   npx prisma db push
   npx prisma studio
   ```

2. **Puerto ocupado**
   ```bash
   # Encontrar proceso
   lsof -i :3000
   # Matar proceso
   kill -9 PID
   ```

3. **Dependencias corruptas**
   ```bash
   # Limpiar e reinstalar
   rm -rf node_modules package-lock.json
   npm install
   ```

---

## 📚 Recursos Adicionales

- [Documentación NestJS](https://docs.nestjs.com/)
- [Documentación Prisma](https://www.prisma.io/docs/)
- [Guía de Despliegue Heroku](https://devcenter.heroku.com/categories/nodejs-support)
- [Docker para Node.js](https://nodejs.org/en/docs/guides/nodejs-docker-webapp/)

---

## 📝 Notas Importantes

1. **Nunca** subir archivos `.env` al repositorio
2. **Siempre** usar variables de entorno para configuraciones sensibles
3. **Verificar** que todas las dependencias estén en `package.json`
4. **Probar** en entorno de staging antes de producción
5. **Configurar** backups de base de datos
6. **Monitorear** logs y métricas en producción

---

*Última actualización: $(date)* 