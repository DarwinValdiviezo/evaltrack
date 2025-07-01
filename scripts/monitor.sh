#!/bin/bash

# Script de monitoreo para EvalTrack
# Uso: ./scripts/monitor.sh [check_type]

set -e

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Función para logging
log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

# Variables
CHECK_TYPE=${1:-all}
APP_URL=${APP_URL:-http://localhost}
LOG_FILE="logs/monitor.log"
ALERT_EMAIL=${ALERT_EMAIL:-admin@company.com}

# Crear directorio de logs si no existe
mkdir -p logs

# Función para health check básico
health_check() {
    log "🏥 Ejecutando health check..."
    
    local response=$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL/health" 2>/dev/null || echo "000")
    
    if [ "$response" = "200" ]; then
        success "Health check exitoso (HTTP $response)"
        return 0
    else
        error "Health check falló (HTTP $response)"
        return 1
    fi
}

# Función para verificar base de datos
database_check() {
    log "🗄️ Verificando conectividad de base de datos..."
    
    # Verificar PostgreSQL
    if command -v psql &> /dev/null; then
        if PGPASSWORD="$POSTGRES_PASSWORD" psql -h "$POSTGRES_HOST" -U "$POSTGRES_USER" -d "$POSTGRES_DB" -c "SELECT 1;" &>/dev/null; then
            success "PostgreSQL conectado correctamente"
        else
            error "Error conectando a PostgreSQL"
            return 1
        fi
    fi
    
    # Verificar MySQL
    if command -v mysql &> /dev/null; then
        if mysql -h "$MYSQL_HOST" -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DB" -e "SELECT 1;" &>/dev/null; then
            success "MySQL conectado correctamente"
        else
            error "Error conectando a MySQL"
            return 1
        fi
    fi
    
    return 0
}

# Función para verificar servicios
services_check() {
    log "🔧 Verificando servicios..."
    
    # Verificar que PHP-FPM esté ejecutándose
    if pgrep -f "php-fpm" > /dev/null; then
        success "PHP-FPM ejecutándose"
    else
        error "PHP-FPM no está ejecutándose"
        return 1
    fi
    
    # Verificar que Nginx esté ejecutándose
    if pgrep -f "nginx" > /dev/null; then
        success "Nginx ejecutándose"
    else
        error "Nginx no está ejecutándose"
        return 1
    fi
    
    # Verificar que Redis esté ejecutándose
    if pgrep -f "redis" > /dev/null; then
        success "Redis ejecutándose"
    else
        warning "Redis no está ejecutándose"
    fi
    
    return 0
}

# Función para verificar espacio en disco
disk_check() {
    log "💾 Verificando espacio en disco..."
    
    local usage=$(df / | tail -1 | awk '{print $5}' | sed 's/%//')
    local available=$(df / | tail -1 | awk '{print $4}')
    
    if [ "$usage" -lt 80 ]; then
        success "Espacio en disco OK ($usage% usado, ${available}KB disponible)"
    elif [ "$usage" -lt 90 ]; then
        warning "Espacio en disco bajo ($usage% usado, ${available}KB disponible)"
    else
        error "Espacio en disco crítico ($usage% usado, ${available}KB disponible)"
        return 1
    fi
    
    return 0
}

# Función para verificar memoria
memory_check() {
    log "🧠 Verificando uso de memoria..."
    
    local total=$(free | grep Mem | awk '{print $2}')
    local used=$(free | grep Mem | awk '{print $3}')
    local usage=$((used * 100 / total))
    
    if [ "$usage" -lt 80 ]; then
        success "Memoria OK ($usage% usado)"
    elif [ "$usage" -lt 90 ]; then
        warning "Memoria alta ($usage% usado)"
    else
        error "Memoria crítica ($usage% usado)"
        return 1
    fi
    
    return 0
}

# Función para verificar logs de errores
logs_check() {
    log "📋 Verificando logs de errores..."
    
    local error_count=0
    
    # Verificar logs de Laravel
    if [ -f "storage/logs/laravel.log" ]; then
        local recent_errors=$(tail -100 storage/logs/laravel.log | grep -c "ERROR\|CRITICAL\|EMERGENCY" || echo "0")
        if [ "$recent_errors" -gt 0 ]; then
            warning "Encontrados $recent_errors errores recientes en logs de Laravel"
            error_count=$((error_count + recent_errors))
        fi
    fi
    
    # Verificar logs de Nginx
    if [ -f "/var/log/nginx/error.log" ]; then
        local nginx_errors=$(tail -50 /var/log/nginx/error.log | grep -c "error" || echo "0")
        if [ "$nginx_errors" -gt 0 ]; then
            warning "Encontrados $nginx_errors errores recientes en logs de Nginx"
            error_count=$((error_count + nginx_errors))
        fi
    fi
    
    if [ "$error_count" -eq 0 ]; then
        success "No se encontraron errores críticos en logs"
    fi
    
    return 0
}

# Función para verificar rendimiento
performance_check() {
    log "⚡ Verificando rendimiento..."
    
    local start_time=$(date +%s.%N)
    local response=$(curl -s -o /dev/null -w "%{time_total}" "$APP_URL" 2>/dev/null || echo "10.0")
    local end_time=$(date +%s.%N)
    
    # Convertir a milisegundos
    local response_time=$(echo "$response * 1000" | bc -l 2>/dev/null || echo "10000")
    
    if (( $(echo "$response_time < 1000" | bc -l) )); then
        success "Rendimiento excelente (${response_time}ms)"
    elif (( $(echo "$response_time < 3000" | bc -l) )); then
        success "Rendimiento bueno (${response_time}ms)"
    elif (( $(echo "$response_time < 5000" | bc -l) )); then
        warning "Rendimiento lento (${response_time}ms)"
    else
        error "Rendimiento crítico (${response_time}ms)"
        return 1
    fi
    
    return 0
}

# Función para verificar certificados SSL
ssl_check() {
    log "🔒 Verificando certificados SSL..."
    
    if [[ "$APP_URL" == https://* ]]; then
        local domain=$(echo "$APP_URL" | sed 's|https://||' | sed 's|/.*||')
        local expiry=$(echo | openssl s_client -servername "$domain" -connect "$domain:443" 2>/dev/null | openssl x509 -noout -dates | grep notAfter | cut -d= -f2)
        
        if [ -n "$expiry" ]; then
            local expiry_date=$(date -d "$expiry" +%s)
            local current_date=$(date +%s)
            local days_left=$(( (expiry_date - current_date) / 86400 ))
            
            if [ "$days_left" -gt 30 ]; then
                success "Certificado SSL válido por $days_left días"
            elif [ "$days_left" -gt 7 ]; then
                warning "Certificado SSL expira en $days_left días"
            else
                error "Certificado SSL expira en $days_left días"
                return 1
            fi
        else
            error "No se pudo verificar el certificado SSL"
            return 1
        fi
    else
        warning "URL no usa HTTPS, saltando verificación SSL"
    fi
    
    return 0
}

# Función para enviar alertas
send_alert() {
    local message="$1"
    local severity="$2"
    
    log "🚨 Enviando alerta: $message"
    
    # Log de la alerta
    echo "$(date): [$severity] $message" >> "$LOG_FILE"
    
    # Enviar email (requiere configuración de mail)
    if command -v mail &> /dev/null; then
        echo "$message" | mail -s "EvalTrack Alert: $severity" "$ALERT_EMAIL"
    fi
    
    # Enviar a Slack (si está configurado)
    if [ -n "$SLACK_WEBHOOK" ]; then
        curl -X POST -H 'Content-type: application/json' \
            --data "{\"text\":\"🚨 EvalTrack Alert: $message\"}" \
            "$SLACK_WEBHOOK" &>/dev/null
    fi
}

# Función para generar reporte
generate_report() {
    log "📊 Generando reporte de monitoreo..."
    
    local report_file="logs/monitor_report_$(date +%Y%m%d_%H%M%S).txt"
    
    {
        echo "=== Reporte de Monitoreo EvalTrack ==="
        echo "Fecha: $(date)"
        echo "URL: $APP_URL"
        echo ""
        
        echo "=== Estado de Servicios ==="
        echo "Health Check: $(health_check >/dev/null 2>&1 && echo "OK" || echo "ERROR")"
        echo "Base de Datos: $(database_check >/dev/null 2>&1 && echo "OK" || echo "ERROR")"
        echo "Servicios: $(services_check >/dev/null 2>&1 && echo "OK" || echo "ERROR")"
        echo ""
        
        echo "=== Recursos del Sistema ==="
        echo "Espacio en disco: $(df / | tail -1 | awk '{print $5}')"
        echo "Memoria: $(free | grep Mem | awk '{print int($3*100/$2)}')%"
        echo ""
        
        echo "=== Rendimiento ==="
        local response_time=$(curl -s -o /dev/null -w "%{time_total}" "$APP_URL" 2>/dev/null || echo "N/A")
        echo "Tiempo de respuesta: ${response_time}s"
        
    } > "$report_file"
    
    success "Reporte generado: $report_file"
}

# Función para monitoreo continuo
continuous_monitor() {
    log "🔄 Iniciando monitoreo continuo..."
    
    local interval=${2:-60}  # Segundos entre verificaciones
    
    while true; do
        log "Ejecutando verificación..."
        
        local failed_checks=0
        
        # Ejecutar todas las verificaciones
        health_check || failed_checks=$((failed_checks + 1))
        database_check || failed_checks=$((failed_checks + 1))
        services_check || failed_checks=$((failed_checks + 1))
        disk_check || failed_checks=$((failed_checks + 1))
        memory_check || failed_checks=$((failed_checks + 1))
        performance_check || failed_checks=$((failed_checks + 1))
        
        if [ "$failed_checks" -gt 0 ]; then
            send_alert "Se detectaron $failed_checks problemas en el sistema" "WARNING"
        fi
        
        log "Esperando $interval segundos para la siguiente verificación..."
        sleep "$interval"
    done
}

# Función principal
main() {
    case $CHECK_TYPE in
        "health")
            health_check
            ;;
        "database")
            database_check
            ;;
        "services")
            services_check
            ;;
        "disk")
            disk_check
            ;;
        "memory")
            memory_check
            ;;
        "logs")
            logs_check
            ;;
        "performance")
            performance_check
            ;;
        "ssl")
            ssl_check
            ;;
        "report")
            generate_report
            ;;
        "continuous")
            continuous_monitor "$@"
            ;;
        "all")
            log "🔍 Ejecutando todas las verificaciones..."
            
            local failed_checks=0
            
            health_check || failed_checks=$((failed_checks + 1))
            database_check || failed_checks=$((failed_checks + 1))
            services_check || failed_checks=$((failed_checks + 1))
            disk_check || failed_checks=$((failed_checks + 1))
            memory_check || failed_checks=$((failed_checks + 1))
            logs_check || failed_checks=$((failed_checks + 1))
            performance_check || failed_checks=$((failed_checks + 1))
            ssl_check || failed_checks=$((failed_checks + 1))
            
            if [ "$failed_checks" -eq 0 ]; then
                success "🎉 Todas las verificaciones pasaron exitosamente"
            else
                error "❌ $failed_checks verificaciones fallaron"
                send_alert "Se detectaron $failed_checks problemas en el sistema" "ERROR"
                exit 1
            fi
            ;;
        *)
            echo "Uso: $0 [check_type]"
            echo ""
            echo "Tipos de verificación:"
            echo "  health       - Health check básico"
            echo "  database     - Verificar conectividad de BD"
            echo "  services     - Verificar servicios del sistema"
            echo "  disk         - Verificar espacio en disco"
            echo "  memory       - Verificar uso de memoria"
            echo "  logs         - Verificar logs de errores"
            echo "  performance  - Verificar rendimiento"
            echo "  ssl          - Verificar certificados SSL"
            echo "  report       - Generar reporte completo"
            echo "  continuous   - Monitoreo continuo"
            echo "  all          - Todas las verificaciones"
            exit 1
            ;;
    esac
}

# Ejecutar función principal
main "$@" 