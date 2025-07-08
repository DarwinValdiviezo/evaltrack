#!/bin/bash

echo "🚀 Desplegando stack de monitoreo EvalTrack..."

# Crear red si no existe
docker network create evaltrack-network 2>/dev/null || echo "Red ya existe"

# Desplegar stack de monitoreo
echo "📊 Iniciando Prometheus..."
docker-compose -f docker-compose.monitoring.yml up -d prometheus

echo "📈 Iniciando Grafana..."
docker-compose -f docker-compose.monitoring.yml up -d grafana

echo "🚨 Iniciando AlertManager..."
docker-compose -f docker-compose.monitoring.yml up -d alertmanager

echo "💻 Iniciando Node Exporter..."
docker-compose -f docker-compose.monitoring.yml up -d node-exporter

# Esperar a que los servicios estén listos
echo "⏳ Esperando a que los servicios estén listos..."
sleep 30

# Verificar estado de los servicios
echo "🔍 Verificando estado de los servicios..."

if curl -f http://localhost:9090/-/healthy >/dev/null 2>&1; then
    echo "✅ Prometheus está funcionando"
else
    echo "❌ Prometheus no responde"
fi

if curl -f http://localhost:3001/api/health >/dev/null 2>&1; then
    echo "✅ Grafana está funcionando"
else
    echo "❌ Grafana no responde"
fi

if curl -f http://localhost:9093/-/healthy >/dev/null 2>&1; then
    echo "✅ AlertManager está funcionando"
else
    echo "❌ AlertManager no responde"
fi

if curl -f http://localhost:9100/metrics >/dev/null 2>&1; then
    echo "✅ Node Exporter está funcionando"
else
    echo "❌ Node Exporter no responde"
fi

echo ""
echo "🎉 Stack de monitoreo desplegado exitosamente!"
echo ""
echo "📊 URLs de acceso:"
echo "  Prometheus: http://localhost:9090"
echo "  Grafana:    http://localhost:3001 (admin/admin123)"
echo "  AlertManager: http://localhost:9093"
echo "  Node Exporter: http://localhost:9100"
echo ""
echo "📋 Próximos pasos:"
echo "  1. Accede a Grafana y configura el datasource de Prometheus"
echo "  2. Importa los dashboards de EvalTrack"
echo "  3. Configura las alertas en Slack"
echo "" 