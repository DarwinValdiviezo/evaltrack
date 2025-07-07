# 🐳 Docker Setup - EvalTrack

## 1. Requisitos Previos

- Docker 24+
- Docker Compose 2+
- Git

## 2. Estructura de Contenedores

- **app**: Contenedor principal Laravel (PHP 8.2+)
- **mysql**: Base de datos de negocio
- **postgres**: Base de datos de usuarios/roles
- **mailpit**: Servicio de correo para pruebas

## 3. Variables de Entorno

Configura tu archivo `.env` (ver ejemplo en el repo) antes de levantar los servicios.

## 4. Comandos de Despliegue

```bash
# Clonar el repositorio
git clone https://github.com/DarwinValdiviezo/evaltrack.git
cd evaltrack

# Copiar variables de entorno
cp env.example .env

# Levantar servicios
docker-compose up -d

# Instalar dependencias y migrar
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app npm install
docker-compose exec app npm run build
```

## 5. Acceso a la Aplicación

- [http://localhost:8000](http://localhost:8000)

## 6. Notas

- Si necesitas reiniciar los contenedores:
  ```bash
  docker-compose down
  docker-compose up -d
  ```
- Para logs:
  ```bash
  docker-compose logs -f
  ```

## 7. Configuración de Docker Hub y GitHub Secrets para EvalTrack

### 1. Configurar Docker Hub

#### Paso 1: Crear cuenta en Docker Hub
1. Ve a [Docker Hub](https://hub.docker.com/)
2. Haz clic en "Sign Up"
3. Usa tu cuenta institucional o crea una nueva cuenta
4. Verifica tu email

#### Paso 2: Crear un Access Token
1. Inicia sesión en Docker Hub
2. Ve a tu perfil → "Account Settings"
3. En el menú lateral, haz clic en "Security"
4. Haz clic en "New Access Token"
5. Dale un nombre como "GitHub Actions EvalTrack"
6. Selecciona "Read & Write" permissions
7. Copia el token generado (lo necesitarás para GitHub Secrets)

#### Paso 3: Verificar tu username de Docker Hub
Tu username de Docker Hub es el que aparece en tu perfil. En nuestro caso, usaremos `darwinvaldiviezo` como ejemplo.

### 2. Configurar GitHub Secrets

#### Paso 1: Ir a tu repositorio en GitHub
1. Ve a tu repositorio: https://github.com/DarwinValdiviezo/evaltrack
2. Haz clic en "Settings" (pestaña)
3. En el menú lateral, haz clic en "Secrets and variables" → "Actions"

#### Paso 2: Agregar los secrets necesarios

##### DOCKER_USERNAME
- **Name**: `DOCKER_USERNAME`
- **Value**: Tu username de Docker Hub (ej: `darwinvaldiviezo`)

##### DOCKER_PASSWORD
- **Name**: `DOCKER_PASSWORD`
- **Value**: El Access Token que generaste en Docker Hub

##### KUBE_CONFIG_DEV (Opcional - para despliegue)
- **Name**: `KUBE_CONFIG_DEV`
- **Value**: Configuración base64 de tu cluster de desarrollo

##### KUBE_CONFIG_STAGING (Opcional - para despliegue)
- **Name**: `KUBE_CONFIG_STAGING`
- **Value**: Configuración base64 de tu cluster de staging

##### KUBE_CONFIG_PROD (Opcional - para despliegue)
- **Name**: `KUBE_CONFIG_PROD`
- **Value**: Configuración base64 de tu cluster de producción

##### SLACK_WEBHOOK (Opcional - para notificaciones)
- **Name**: `SLACK_WEBHOOK`
- **Value**: URL del webhook de Slack para notificaciones

### 3. Configurar el Pipeline

#### Paso 1: Crear la estructura de carpetas
```bash
mkdir -p .github/workflows
```

#### Paso 2: El archivo ya está creado
El archivo `.github/workflows/ci-cd.yml` ya está configurado con:
- Tests automáticos
- Build de imagen Docker
- Despliegue a diferentes entornos
- Monitoreo post-despliegue

#### Paso 3: Personalizar el pipeline
En el archivo `ci-cd.yml`, puedes modificar:

```yaml
env:
  REGISTRY: docker.io
  IMAGE_NAME: darwinvaldiviezo/evaltrack  # Cambia por tu username
```

### 4. Probar el Pipeline

#### Paso 1: Hacer commit y push
```bash
git add .github/workflows/ci-cd.yml
git commit -m "Agregar pipeline CI/CD"
git push origin main
```

#### Paso 2: Verificar en GitHub Actions
1. Ve a tu repositorio en GitHub
2. Haz clic en la pestaña "Actions"
3. Deberías ver el workflow ejecutándose

### 5. Configuración para Desarrollo Local

#### Docker Compose (sin Docker Hub)
Si quieres probar localmente sin Docker Hub:

```bash
# Construir imagen localmente
docker build -t evaltrack:local .

# Ejecutar con docker-compose
docker-compose up -d
```

#### Variables de entorno locales
Crea un archivo `.env.local`:

```env
APP_NAME=EvalTrack
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Base de datos PostgreSQL
DB_CONNECTION=pgsql
DB_PGSQL_HOST=postgres
DB_PGSQL_DATABASE=evaltrack_users
DB_PGSQL_USERNAME=evaltrack_user
DB_PGSQL_PASSWORD=password

# Base de datos MySQL
DB_HOST=mysql
DB_DATABASE=evaltrack_business
DB_USERNAME=evaltrack_user
DB_PASSWORD=password

# Redis
REDIS_HOST=redis
```

### 6. Troubleshooting

#### Error: "authentication required"
- Verifica que `DOCKER_USERNAME` y `DOCKER_PASSWORD` estén configurados correctamente
- Asegúrate de que el Access Token tenga permisos de "Read & Write"

#### Error: "repository does not exist"
- Verifica que el nombre de la imagen en `IMAGE_NAME` coincida con tu username
- Asegúrate de que el repositorio exista en Docker Hub

#### Error: "permission denied"
- Verifica que el Access Token tenga los permisos correctos
- Asegúrate de que la cuenta de Docker Hub esté verificada

### 7. Próximos Pasos

1. **Configurar Docker Hub** con tu cuenta
2. **Agregar los secrets** en GitHub
3. **Hacer push** del código para activar el pipeline
4. **Configurar entornos de despliegue** (opcional)
5. **Configurar monitoreo** (opcional)

### 8. Comandos Útiles

```bash
# Verificar que Docker funciona
docker --version
docker-compose --version

# Probar build local
docker build -t evaltrack:test .

# Ejecutar tests localmente
composer test

# Verificar sintaxis del workflow
# (GitHub Actions valida automáticamente)
```

---

**Nota**: Si tienes problemas con la cuenta institucional de Docker Hub, puedes:
1. Crear una cuenta personal gratuita
2. Usar GitHub Container Registry en lugar de Docker Hub
3. Usar solo Docker Compose para desarrollo local 