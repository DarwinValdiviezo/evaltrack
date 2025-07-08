# rollback.sh

Este script sirve para volver a una versión anterior de la aplicación si algo sale mal en producción. Hace el rollback, verifica que todo esté bien y notifica al equipo. Es como un botón de emergencia.

## Fragmento importante:
```bash
./rollback.sh prod previous
```

## ¿Por qué es importante?
- Permite regresar rápido si el despliegue falla.
- Hace health checks y smoke tests después del rollback.
- Notifica al equipo para que todos estén enterados.

**Recomendación:**
Úsalo solo si es necesario y siempre revisa que la versión a la que vuelves es estable. 