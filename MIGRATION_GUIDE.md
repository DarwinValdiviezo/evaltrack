# Guía de Migraciones - EvalTrack

## 📊 Arquitectura de Base de Datos

EvalTrack utiliza una arquitectura híbrida con dos bases de datos separadas:

### 🐘 PostgreSQL - Usuarios y Roles
- **Propósito**: Gestión de usuarios, autenticación, roles y permisos
- **Conexión**: `pgsql`
- **Migraciones**: `database/migrations/users/`

### 🐬 MySQL - Datos de Negocio
- **Propósito**: Datos operativos del negocio (empleados, eventos, asistencias, evaluaciones)
- **Conexión**: `mysql_business`
- **Migraciones**: `database/migrations/business/`

## 🚀 Comandos de Migración

### Comando Personalizado (Recomendado)
```bash
# Migrar ambas bases de datos
php artisan migrate:all

# Migrar con fresh (eliminar todas las tablas y recrear)
php artisan migrate:all --fresh

# Migrar con fresh y seed
php artisan migrate:all --fresh --seed
```

### Comandos Individuales
```bash
# Migrar solo PostgreSQL (usuarios/roles)
php artisan migrate --database=pgsql --path=database/migrations/users

# Migrar solo MySQL (negocio)
php artisan migrate --database=mysql_business --path=database/migrations/business

# Hacer fresh de PostgreSQL
php artisan migrate:fresh --database=pgsql --path=database/migrations/users

# Hacer fresh de MySQL
php artisan migrate:fresh --database=mysql_business --path=database/migrations/business
```

## 📁 Estructura de Migraciones

```
database/migrations/
├── users/                    # PostgreSQL - Usuarios y Roles
│   ├── 0001_01_01_000001_create_cache_table.php
│   ├── 0001_01_01_000002_create_jobs_table.php
│   ├── 2025_05_27_040416_create_users_table.php
│   ├── 2025_06_30_001507_create_permission_tables.php
│   ├── 2025_06_30_002845_create_sessions_table.php
│   ├── 2025_06_30_045110_add_fields_to_users_table.php
│   ├── 2025_06_30_144929_add_deleted_at_to_roles_table.php
│   └── 2025_06_30_144930_fix_deleted_at_roles_table.php
│
└── business/                 # MySQL - Datos de Negocio
    ├── 2025_05_27_040711_create_eventos_table.php
    ├── 2025_05_27_040712_create_employees_table.php
    ├── 2025_05_27_040713_create_asistencias_table.php
    ├── 2025_05_27_040717_create_evaluaciones_table.php
    ├── 2025_06_30_013525_add_fecha_hora_to_asistencias_table.php
    ├── 2025_06_30_013532_add_preguntas_to_evaluaciones_table.php
    ├── 2025_06_30_013644_add_preguntas_to_evaluaciones_table.php
    ├── 2025_06_30_020626_add_disponible_to_evaluaciones_status_enum.php
    ├── 2025_06_30_040126_add_borrador_to_eventos_estado_enum.php
    ├── 2025_06_30_042855_add_unique_evento_empleado_to_asistencias.php
    └── 2025_06_30_050241_add_estado_to_employees_table.php
```

## 🔧 Crear Nuevas Migraciones

### Para PostgreSQL (Usuarios/Roles)
```bash
php artisan make:migration create_nueva_tabla_usuarios --path=database/migrations/users
```

### Para MySQL (Negocio)
```bash
php artisan make:migration create_nueva_tabla_negocio --path=database/migrations/business
```

## 📝 Configuración de Migraciones

### Migración PostgreSQL
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('pgsql')->create('nueva_tabla', function (Blueprint $table) {
            $table->id();
            // ... columnas
        });
    }

    public function down(): void
    {
        Schema::connection('pgsql')->dropIfExists('nueva_tabla');
    }
};
```

### Migración MySQL
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mysql_business')->create('nueva_tabla', function (Blueprint $table) {
            $table->id();
            // ... columnas
        });
    }

    public function down(): void
    {
        Schema::connection('mysql_business')->dropIfExists('nueva_tabla');
    }
};
```

## 🧪 Testing

### Configuración de Testing
```env
# .env.testing
DB_CONNECTION=pgsql
DB_PGSQL_HOST=127.0.0.1
DB_PGSQL_PORT=5432
DB_PGSQL_DATABASE=evaltrack_users_test
DB_PGSQL_USERNAME=postgres
DB_PGSQL_PASSWORD=password

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=evaltrack_business_test
DB_USERNAME=root
DB_PASSWORD=root_password
```

### Ejecutar Tests
```bash
# Ejecutar tests con migraciones automáticas
php artisan test

# Ejecutar tests específicos
php artisan test --testsuite=Feature
```

## 🐳 Docker

### Migraciones en Docker
```bash
# Ejecutar migraciones en contenedor
docker-compose exec app php artisan migrate:all

# Ejecutar con fresh
docker-compose exec app php artisan migrate:all --fresh --seed
```

## 🔄 CI/CD Pipeline

El pipeline de GitHub Actions ejecuta automáticamente:

1. **Migración PostgreSQL**: `php artisan migrate --database=pgsql --path=database/migrations/users`
2. **Migración MySQL**: `php artisan migrate --database=mysql_business --path=database/migrations/business`
3. **Seeders**: `php artisan db:seed`

## ⚠️ Consideraciones Importantes

### Orden de Migraciones
- Las migraciones de PostgreSQL deben ejecutarse primero
- Las migraciones de MySQL dependen de que PostgreSQL esté disponible

### Relaciones Entre Bases
- Los modelos de negocio pueden relacionarse con usuarios de PostgreSQL
- Usar `user_id` como clave foránea para referenciar usuarios

### Rollback
```bash
# Rollback de ambas bases
php artisan migrate:rollback --database=pgsql --path=database/migrations/users
php artisan migrate:rollback --database=mysql_business --path=database/migrations/business
```

## 🆘 Solución de Problemas

### Error: "Table doesn't exist"
- Verificar que la migración esté en la carpeta correcta
- Verificar que la conexión esté configurada correctamente
- Ejecutar migraciones en el orden correcto

### Error: "Connection refused"
- Verificar que las bases de datos estén ejecutándose
- Verificar credenciales en `.env`
- Verificar puertos y hosts

### Error: "Foreign key constraint fails"
- Verificar que las tablas referenciadas existan
- Verificar el orden de ejecución de migraciones
- Usar `--fresh` para recrear desde cero 