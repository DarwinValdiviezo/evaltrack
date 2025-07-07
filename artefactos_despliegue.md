# Artefactos de Despliegue – EvalTrack

## Introducción

En el proceso de despliegue y operación de EvalTrack, es fundamental identificar y gestionar los artefactos clave que garantizan la trazabilidad, la calidad y la recuperación ante cualquier eventualidad. A continuación, se detallan los principales artefactos considerados en nuestro ciclo DevOps.

---

## 1. Código Fuente

El corazón del sistema reside en el repositorio de código, donde se almacena toda la lógica de negocio, controladores, modelos, vistas y scripts necesarios para el funcionamiento de EvalTrack.

- **Repositorio:** GitHub (rama principal y tags de versión)
- **Lenguajes:** PHP (Laravel), JavaScript, SCSS
- **Incluye:** migraciones, seeders, archivos de configuración, scripts de automatización

---

## 2. Artefactos de Build

Durante el proceso de integración continua, se generan artefactos que serán utilizados en los entornos de prueba y producción.

- **Dependencias PHP:** Paquetes instalados vía Composer
- **Dependencias JS:** Paquetes instalados vía NPM
- **Assets compilados:** Archivos JavaScript y CSS generados por Vite listos para producción

---

## 3. Contenedores

Para facilitar la portabilidad y el despliegue, se utilizan imágenes Docker que encapsulan la aplicación y sus dependencias.

- **Imagen de la aplicación:** evaltrack-app:1.0.0
- **Imagen de Nginx:** evaltrack-nginx:1.0.0
- **Orquestación:** docker-compose.yml para levantar todos los servicios necesarios

---

## 4. Migraciones y Seeders

La estructura y los datos iniciales de las bases de datos se gestionan mediante migraciones y seeders versionados junto al código.

- **Migraciones:** Scripts para crear y modificar tablas en PostgreSQL y MySQL
- **Seeders:** Datos de ejemplo y usuarios iniciales para pruebas y desarrollo

---

## 5. Configuraciones

La correcta parametrización del sistema es esencial para su funcionamiento en diferentes entornos.

- **Variables de entorno:** Archivo .env (no versionado)
- **Archivos de configuración:** Carpeta config/ de Laravel
- **Configuraciones de servicios:** Nginx, PHP-FPM, Redis, etc.

---

## 6. Documentación

Toda la información relevante para el despliegue, operación y mantenimiento del sistema está documentada y disponible para el equipo.

- **Manuales de despliegue:** DOCUMENTO_DESPLIEGUE.md, DOCKER_SETUP.md
- **Guías de migración:** MIGRATION_GUIDE.md
- **Documentación de pipeline:** README_PIPELINE.md, RESUMEN_PIPELINE.md

---

## 7. Artefactos de CI/CD

El pipeline de integración y despliegue continuo genera y almacena artefactos que permiten la automatización y el control de calidad.

- **Definiciones de pipeline:** GitHub Actions, Jenkins, GitLab CI, etc.
- **Logs de build y despliegue:** Para auditoría y troubleshooting
- **Reportes de pruebas:** Resultados de tests automatizados

---

## 8. Bases de Datos

La información es uno de los activos más importantes, por lo que se gestionan scripts y respaldos para garantizar su integridad.

- **Scripts de creación y backup:** Para restauración rápida en caso de incidentes
- **Dumps de respaldo:** Copias de seguridad periódicas

---

## 9. Registros y Monitoreo

El monitoreo y la trazabilidad de la operación se logran mediante logs y herramientas de observabilidad.

- **Logs de aplicación:** Almacenados en storage/logs/
- **Configuración de monitoreo:** Prometheus, Grafana, etc. (si aplica)

---

## Tabla Resumen de Artefactos

| Artefacto                  | Ubicación/Repositorio                | Versión/Tag         | Descripción                         |
|----------------------------|--------------------------------------|---------------------|-------------------------------------|
| Código fuente              | GitHub (main, tags)                  | v1.0.0              | Código Laravel y frontend           |
| Imagen Docker App          | Docker Hub / local registry           | evaltrack-app:1.0.0 | Contenedor de la app                |
| Imagen Docker Nginx        | Docker Hub / local registry           | evaltrack-nginx:1.0.0| Contenedor Nginx                    |
| Assets compilados          | public/js, public/css                | hash de build       | JS y CSS listos para producción     |
| Migraciones y seeders      | database/migrations, database/seeders| commit/tag          | Scripts de BD                       |
| Configuración de entorno   | .env, config/                        | commit/tag          | Variables y settings                |
| Documentación              | /docs, *.md                          | commit/tag          | Manuales y guías                    |
| Pipeline CI/CD             | .github/workflows/, Jenkinsfile, etc.| commit/tag          | Definición de automatización        |
| Backups de BD              | storage/backups/                     | fecha/hora          | Respaldo de datos                   |

---

**Este documento resume los artefactos clave que acompañan el ciclo de vida de EvalTrack, asegurando un despliegue seguro, trazable y eficiente.** 