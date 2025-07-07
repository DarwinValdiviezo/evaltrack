#!/bin/bash

# Script de health check para EvalTrack
# Uso: ./scripts/health-check.sh

set -e

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Variables
APP_URL="http://localhost"
HEALTH_ENDPOINT="/health"
TIMEOUT=30
RETRIES=3

# Función para logging
log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1"
    exit 1
}

success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log "🏥 Iniciando health check de EvalTrack..."

# Función para verificar si el servidor está ejecutándose
check_server_running() {
    log "🔍 Verificando si el servidor está ejecutándose..."
    
    if curl -f "$APP_URL" > /dev/null 2>&1; then
        success "Servidor web respondiendo"
        return 0
    else
        error "Servidor web no responde en $APP_URL"
        return 1
    fi
}

# Función para verificar health endpoint
check_health_endpoint() {
    log "🏥 Verificando health endpoint..."
    
    local response
    local retry_count=0
    
    while [ $retry_count -lt $RETRIES ]; do
        if response=$(curl -f -s "$APP_URL$HEALTH_ENDPOINT" 2>/dev/null); then
            success "Health endpoint respondiendo"
            
            # Parsear respuesta JSON si es posible
            if command -v jq &> /dev/null; then
                local status=$(echo "$response" | jq -r '.status' 2>/dev/null || echo "unknown")
                local version=$(echo "$response" | jq -r '.version' 2>/dev/null || echo "unknown")
                
                log "📊 Estado: $status"
                log "📦 Versión: $version"
                
                # Verificar estado de bases de datos
                local postgres_status=$(echo "$response" | jq -r '.database.postgresql' 2>/dev/null || echo "unknown")
                local mysql_status=$(echo "$response" | jq -r '.database.mysql' 2>/dev/null || echo "unknown")
                
                log "🗄️ PostgreSQL: $postgres_status"
                log "🗄️ MySQL: $mysql_status"
                
                if [ "$postgres_status" = "error" ] || [ "$mysql_status" = "error" ]; then
                    warning "Problemas de conectividad con bases de datos"
                fi
            else
                log "📄 Respuesta del health endpoint: $response"
            fi
            
            return 0
        else
            retry_count=$((retry_count + 1))
            warning "Intento $retry_count de $RETRIES falló"
            
            if [ $retry_count -lt $RETRIES ]; then
                log "⏳ Esperando 5 segundos antes del siguiente intento..."
                sleep 5
            fi
        fi
    done
    
    error "Health endpoint no responde después de $RETRIES intentos"
    return 1
}

# Función para verificar conectividad de bases de datos
check_database_connectivity() {
    log "🗄️ Verificando conectividad de bases de datos..."
    
    # Verificar PostgreSQL
    if command -v psql &> /dev/null; then
        if psql -h localhost -U evaltrack_user -d evaltrack_users -c "SELECT 1;" > /dev/null 2>&1; then
            success "PostgreSQL conectado"
        else
            warning "No se pudo conectar a PostgreSQL"
        fi
    else
        warning "psql no está instalado - no se puede verificar PostgreSQL"
    fi
    
    # Verificar MySQL
    if command -v mysql &> /dev/null; then
        if mysql -h localhost -u evaltrack_user -p -e "SELECT 1;" > /dev/null 2>&1; then
            success "MySQL conectado"
        else
            warning "No se pudo conectar a MySQL"
        fi
    else
        warning "mysql no está instalado - no se puede verificar MySQL"
    fi
}

# Función para verificar servicios Docker (si están ejecutándose)
check_docker_services() {
    log "🐳 Verificando servicios Docker..."
    
    if command -v docker-compose &> /dev/null; then
        if [ -f "docker-compose.yml" ]; then
            local services_status=$(docker-compose ps --services --filter "status=running" 2>/dev/null || echo "")
            
            if [ -n "$services_status" ]; then
                success "Servicios Docker ejecutándose:"
                echo "$services_status" | while read service; do
                    log "  ✅ $service"
                done
            else
                warning "No hay servicios Docker ejecutándose"
            fi
        else
            log "No se encontró docker-compose.yml"
        fi
    else
        log "Docker Compose no está instalado"
    fi
}

# Función para verificar recursos del sistema
check_system_resources() {
    log "💻 Verificando recursos del sistema..."
    
    # Verificar uso de CPU
    if command -v top &> /dev/null; then
        local cpu_usage=$(top -bn1 | grep "Cpu(s)" | awk '{print $2}' | cut -d'%' -f1)
        log "📊 Uso de CPU: ${cpu_usage}%"
        
        if (( $(echo "$cpu_usage > 80" | bc -l) )); then
            warning "Uso de CPU alto: ${cpu_usage}%"
        fi
    fi
    
    # Verificar uso de memoria
    if command -v free &> /dev/null; then
        local mem_info=$(free -m | grep Mem)
        local mem_total=$(echo $mem_info | awk '{print $2}')
        local mem_used=$(echo $mem_info | awk '{print $3}')
        local mem_percent=$((mem_used * 100 / mem_total))
        
        log "📊 Memoria: ${mem_used}MB / ${mem_total}MB (${mem_percent}%)"
        
        if [ $mem_percent -gt 80 ]; then
            warning "Uso de memoria alto: ${mem_percent}%"
        fi
    fi
    
    # Verificar espacio en disco
    if command -v df &> /dev/null; then
        local disk_usage=$(df -h . | tail -1 | awk '{print $5}' | cut -d'%' -f1)
        log "📊 Espacio en disco: ${disk_usage}% usado"
        
        if [ $disk_usage -gt 80 ]; then
            warning "Espacio en disco bajo: ${disk_usage}% usado"
        fi
    fi
}

# Función para verificar logs de errores
check_error_logs() {
    log "📋 Verificando logs de errores..."
    
    if [ -f "storage/logs/laravel.log" ]; then
        local recent_errors=$(tail -n 50 storage/logs/laravel.log | grep -i "error\|exception\|fatal" | wc -l)
        
        if [ $recent_errors -gt 0 ]; then
            warning "Se encontraron $recent_errors errores recientes en los logs"
            log "Últimos errores:"
            tail -n 10 storage/logs/laravel.log | grep -i "error\|exception\|fatal" || true
        else
            success "No se encontraron errores recientes en los logs"
        fi
    else
        log "No se encontró archivo de logs"
    fi
}

# Función para verificar endpoints críticos
check_critical_endpoints() {
    log "🔗 Verificando endpoints críticos..."
    
    local endpoints=(
        "/"
        "/login"
        "/api/health"
    )
    
    for endpoint in "${endpoints[@]}"; do
        if curl -f -s "$APP_URL$endpoint" > /dev/null 2>&1; then
            success "Endpoint $endpoint respondiendo"
        else
            warning "Endpoint $endpoint no responde"
        fi
    done
}

# Función principal
main() {
    local all_checks_passed=true
    
    # Ejecutar todas las verificaciones
    check_server_running || all_checks_passed=false
    check_health_endpoint || all_checks_passed=false
    check_database_connectivity
    check_docker_services
    check_system_resources
    check_error_logs
    check_critical_endpoints
    
    # Resultado final
    if [ "$all_checks_passed" = true ]; then
        success "🎉 Health check completado exitosamente"
        log "✅ La aplicación está funcionando correctamente"
        exit 0
    else
        error "❌ Health check falló - hay problemas que resolver"
        exit 1
    fi
}

# Ejecutar función principal
main "$@" 