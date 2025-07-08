# docker-compose.prod.yml

Este archivo sirve para levantar todos los servicios necesarios para producción con un solo comando. Aquí se definen los contenedores de backend, frontend, base de datos y monitoreo, y cómo se conectan entre sí.

## Fragmento importante:
```yaml
services:
  backend:
    image: ghcr.io/your-org/backend:latest
    ports:
      - "3000:3000"
  frontend:
    image: ghcr.io/your-org/frontend:latest
    ports:
      - "80:80"
```

## ¿Por qué es importante?
- Facilita levantar todo el sistema en local o en servidores.
- Asegura que todos los servicios usen la misma configuración.
- Permite probar cambios antes de desplegar a Kubernetes.

**Recomendación:**
Verifica que las versiones de las imágenes sean las correctas y que los puertos no estén ocupados. 