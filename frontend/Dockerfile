# ========================================
# EVALTRACK v2.0.0 - FRONTEND DOCKERFILE
# ========================================

# Etapa de build
FROM node:20-alpine AS builder

# Establecer directorio de trabajo
WORKDIR /app

# Copiar archivos de dependencias
COPY package*.json ./

# Instalar dependencias
RUN npm ci

# Copiar código fuente
COPY . .

# Build de la aplicación
RUN npm run build

# ========================================
# Etapa de producción con Nginx
FROM nginx:alpine AS production

# Copiar configuración de Nginx
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Copiar archivos buildados
COPY --from=builder /app/dist /usr/share/nginx/html

# Crear archivo de health check
RUN echo "healthy" > /usr/share/nginx/html/health

# Exponer puerto
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
  CMD curl -f http://localhost/health || exit 1

# Comando de inicio
CMD ["nginx", "-g", "daemon off;"] 