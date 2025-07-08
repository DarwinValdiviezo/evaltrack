# deployment.yaml

Este archivo sirve para definir cómo se va a desplegar la aplicación en Kubernetes. Aquí se indica cuántas réplicas queremos, qué imagen de Docker usar, variables de entorno y los probes de salud. Es como la receta para que Kubernetes sepa cómo correr nuestro backend.

## Fragmento importante:
```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: backend-prod
spec:
  replicas: 3
  template:
    spec:
      containers:
      - name: backend
        image: ghcr.io/your-org/backend:latest
        ports:
        - containerPort: 3000
```

## ¿Por qué es importante?
- Permite escalar la app fácilmente.
- Define cómo se actualiza la app (estrategia Blue/Green).
- Incluye health checks para saber si está viva.

**Recomendación:**
Siempre revisa que la imagen y las variables de entorno sean las correctas antes de desplegar. 