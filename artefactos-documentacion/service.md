# service.yaml

Este archivo define los servicios de Kubernetes, que son como puertas de entrada para acceder a las aplicaciones dentro del cluster. Aquí se exponen los puertos del backend, frontend y base de datos para que puedan comunicarse entre sí o desde fuera (si se configura así).

## Fragmento importante:
```yaml
apiVersion: v1
kind: Service
metadata:
  name: backend-service
spec:
  selector:
    app: backend
  ports:
  - port: 80
    targetPort: 3000
```

## ¿Por qué es importante?
- Permite que otros servicios o usuarios accedan a la app.
- Hace balanceo de carga entre los pods.
- Es necesario para exponer la app fuera del cluster.

**Recomendación:**
No expongas puertos innecesarios y usa ClusterIP salvo que realmente necesites acceso externo. 