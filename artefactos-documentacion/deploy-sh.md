# deploy.sh

Este script es para automatizar el despliegue de la aplicación. Hace validaciones, backups, despliega usando Blue/Green y monitorea si todo salió bien. Así no tienes que hacer todo a mano y reduces errores.

## Fragmento importante:
```bash
./deploy.sh prod v1.0.0
```

## ¿Por qué es importante?
- Ahorra tiempo y evita errores humanos.
- Hace backup antes de desplegar (por si algo sale mal).
- Permite rollback automático si detecta fallos.

**Recomendación:**
Siempre revisa que los parámetros sean correctos y que tengas acceso al cluster antes de correrlo. 