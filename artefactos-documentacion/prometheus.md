# prometheus.yml

Este archivo es la configuración de Prometheus, que es la herramienta que recolecta y guarda todas las métricas del sistema. Aquí se le dice a Prometheus a qué servicios debe ir a preguntar por métricas y cada cuánto tiempo.

## Fragmento importante:
```yaml
scrape_configs:
  - job_name: 'backend'
    static_configs:
      - targets: ['backend-service:3000']
```

## ¿Por qué es importante?
- Permite monitorear la salud y el rendimiento de la app.
- Es la base para tener alertas y dashboards.
- Ayuda a detectar problemas antes de que sean graves.

**Recomendación:**
Asegúrate de que los targets sean correctos y que Prometheus tenga acceso a los servicios. 