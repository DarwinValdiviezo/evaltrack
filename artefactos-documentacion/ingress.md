# ingress.yaml

El archivo de Ingress sirve para definir las rutas de acceso desde internet hacia los servicios del cluster. Aquí se configuran los dominios, los certificados SSL y las reglas para que el tráfico llegue al backend, frontend o herramientas de monitoreo.

## Fragmento importante:
```yaml
apiVersion: networking.k8s.io/v1
kind: Ingress
metadata:
  name: asistencia-ingress
spec:
  rules:
  - host: api.asistencia.com
    http:
      paths:
      - path: /
        backend:
          service:
            name: backend-service
            port:
              number: 80
```

## ¿Por qué es importante?
- Permite usar dominios bonitos y certificados SSL.
- Controla el acceso externo a los servicios.
- Se pueden poner reglas de seguridad y autenticación.

**Recomendación:**
Siempre usa HTTPS y revisa bien las reglas para no exponer servicios sensibles. 