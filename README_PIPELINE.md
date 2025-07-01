# 🚀 Pipeline CI/CD - EvalTrack

## 📋 Resumen

Este proyecto incluye un pipeline CI/CD completo configurado con GitHub Actions que automatiza:
- ✅ Tests automáticos
- ✅ Análisis de código
- ✅ Build de Docker
- ✅ Despliegue (opcional)

## 🎯 Opciones de Pipeline

### 1. Pipeline Simple (Recomendado para empezar)
**Archivo**: `.github/workflows/ci-simple.yml`

**Características**:
- ✅ Tests automáticos (PHPUnit)
- ✅ Análisis estático (PHPStan)
- ✅ Verificación de estilo (Laravel Pint)
- ✅ Análisis de seguridad (Enlightn)
- ✅ Build de Docker (sin push)
- ✅ Cobertura de código
- ❌ No requiere Docker Hub

### 2. Pipeline Completo (Para producción)
**Archivo**: `.github/workflows/ci-cd.yml`

**Características**:
- ✅ Todo lo del pipeline simple
- ✅ Push automático a Docker Hub
- ✅ Despliegue automático a entornos
- ✅ Monitoreo post-despliegue
- ❌ Requiere Docker Hub configurado

## 🛠️ Configuración Rápida

### Paso 1: Verificar archivos
Los archivos del pipeline ya están creados:
```
.github/workflows/
├── ci-simple.yml    # Pipeline simple
└── ci-cd.yml        # Pipeline completo
```

### Paso 2: Hacer commit y push
```bash
# Agregar los archivos del pipeline
git add .github/workflows/

# Hacer commit
git commit -m "Agregar pipeline CI/CD"

# Hacer push
git push origin main
```

### Paso 3: Verificar en GitHub
1. Ve a tu repositorio: https://github.com/DarwinValdiviezo/evaltrack
2. Haz clic en la pestaña "Actions"
3. Deberías ver el workflow ejecutándose

## 🔧 Configuración de Docker Hub (Opcional)

Si quieres usar el pipeline completo con Docker Hub:

### 1. Crear cuenta en Docker Hub
1. Ve a [Docker Hub](https://hub.docker.com/)
2. Haz clic en "Sign Up"
3. Usa tu cuenta institucional o crea una nueva
4. Verifica tu email

### 2. Crear Access Token
1. Inicia sesión en Docker Hub
2. Ve a tu perfil → "Account Settings"
3. En el menú lateral, haz clic en "Security"
4. Haz clic en "New Access Token"
5. Dale un nombre como "GitHub Actions EvalTrack"
6. Selecciona "Read & Write" permissions
7. Copia el token generado

### 3. Configurar GitHub Secrets
1. Ve a tu repositorio: https://github.com/DarwinValdiviezo/evaltrack
2. Haz clic en "Settings" (pestaña)
3. En el menú lateral, haz clic en "Secrets and variables" → "Actions"
4. Agrega los siguientes secrets:

#### DOCKER_USERNAME
- **Name**: `DOCKER_USERNAME`
- **Value**: Tu username de Docker Hub (ej: `darwinvaldiviezo`)

#### DOCKER_PASSWORD
- **Name**: `DOCKER_PASSWORD`
- **Value**: El Access Token que generaste en Docker Hub

## 🧪 Probar Localmente

### Con Docker Compose
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

### Con Docker Build
```bash
# Construir imagen localmente
docker build -t evaltrack:local .

# Ejecutar contenedor
docker run -p 8000:80 evaltrack:local
```

### Tests Locales
```bash
# Instalar dependencias
composer install
npm install

# Ejecutar tests
php artisan test

# Verificar estilo de código
./vendor/bin/pint --test

# Análisis estático
./vendor/bin/phpstan analyse app --level=8
```

## 📊 Monitoreo del Pipeline

### Estados del Workflow
- 🟢 **Success**: Todos los tests pasaron
- 🔴 **Failure**: Algunos tests fallaron
- 🟡 **Pending**: En espera de ejecución

### Logs Importantes
- **Tests**: Verifica que todos los tests pasen
- **Build**: Verifica que Docker se construya correctamente
- **Analysis**: Verifica que no haya errores de código

## 🔍 Troubleshooting

### Error: "composer install failed"
- Verifica que `composer.json` esté correcto
- Asegúrate de que las dependencias sean compatibles

### Error: "tests failed"
- Revisa los logs de los tests
- Verifica que las bases de datos de prueba estén configuradas

### Error: "Docker build failed"
- Verifica que el `Dockerfile` esté correcto
- Asegúrate de que todas las dependencias estén incluidas

### Error: "authentication required" (Docker Hub)
- Verifica que `DOCKER_USERNAME` y `DOCKER_PASSWORD` estén configurados
- Asegúrate de que el Access Token tenga permisos correctos

## 📈 Métricas del Pipeline

### Tiempos de Ejecución
- **Tests**: ~5-10 minutos
- **Build Docker**: ~3-5 minutos
- **Análisis**: ~2-3 minutos

### Cobertura de Código
- **Mínimo requerido**: 80%
- **Actual**: Se calcula automáticamente

## 🎯 Próximos Pasos

1. **Configurar Docker Hub** (si quieres pipeline completo)
2. **Agregar más tests** para aumentar cobertura
3. **Configurar entornos de despliegue** (staging, producción)
4. **Configurar monitoreo** (Slack, email)
5. **Optimizar tiempos de ejecución**

## 📚 Documentación Adicional

- **DOCKER_SETUP.md**: Guía completa de Docker Hub
- **DOCUMENTO_DESPLIEGUE.md**: Documento completo de DevOps
- **README.md**: Documentación general del proyecto

## 🤝 Contribuir

Para contribuir al pipeline:

1. Haz fork del repositorio
2. Crea una rama para tu feature
3. Haz los cambios necesarios
4. Ejecuta los tests localmente
5. Haz commit y push
6. Crea un Pull Request

---

**Nota**: El pipeline está configurado para funcionar inmediatamente. Solo necesitas hacer commit y push para activarlo. 