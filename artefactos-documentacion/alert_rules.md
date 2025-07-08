# alert_rules.yml

Este archivo define las reglas de alertas para Prometheus. Aquí se ponen las condiciones para que se dispare una alerta, por ejemplo si hay muchos errores o si el sistema está caído.

## Fragmento importante:
```yaml
- alert: HighErrorRate
  expr: rate(http_requests_total{status=~"5.."}[5m]) > 0.05
  for: 2m
  labels:
    severity: critical
  annotations:
    summary: "Tasa de error alta en backend"
```

## ¿Por qué es importante?
- Permite enterarse rápido si algo va mal.
- Ayuda a reaccionar antes de que el usuario se queje.
- Se puede conectar con Slack, email, etc.

**Recomendación:**
Ajusta los umbrales según la realidad de tu sistema para evitar alertas falsas. 