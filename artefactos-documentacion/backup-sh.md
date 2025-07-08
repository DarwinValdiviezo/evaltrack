# backup.sh

Este script hace un backup automático de la base de datos, configuraciones y logs. Así puedes restaurar todo si pasa algo grave. También limpia los backups viejos para no llenar el disco.

## Fragmento importante:
```bash
./backup.sh prod full
```

## ¿Por qué es importante?
- Protege la información ante errores o ataques.
- Permite restaurar el sistema rápido.
- Mantiene el espacio en disco bajo control.

**Recomendación:**
Programa backups regulares y revisa que los archivos se estén generando bien. 