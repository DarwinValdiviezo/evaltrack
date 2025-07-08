# grafana-dashboards.json

Este archivo contiene la configuración de los dashboards de Grafana. Aquí se definen los gráficos y paneles que muestran las métricas más importantes del sistema, como usuarios activos, errores, uso de CPU, etc.

## Fragmento importante:
```json
{
  "title": "Sistema de Asistencia - Dashboard Principal",
  "panels": [
    { "title": "Estado del Sistema", "type": "stat" },
    { "title": "Requests por Segundo", "type": "graph" }
  ]
}
```

## ¿Por qué es importante?
- Permite ver el estado del sistema de un vistazo.
- Ayuda a detectar tendencias y problemas.
- Es útil para presentaciones y reportes.

**Recomendación:**
Personaliza los dashboards con las métricas que más te interesen y compártelos con tu equipo. 