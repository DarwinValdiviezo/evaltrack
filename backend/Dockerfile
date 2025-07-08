# ========================================
# EVALTRACK v2.0.0 - BACKEND DOCKERFILE
# ========================================

FROM node:18-alpine

# Instalar dumb-init
RUN apk add --no-cache dumb-init

# Crear usuario no-root
RUN addgroup -g 1001 -S nodejs
RUN adduser -S nestjs -u 1001

# Establecer directorio de trabajo
WORKDIR /app

# Copiar archivos de dependencias
COPY package*.json ./
COPY prisma ./prisma/

# Instalar dependencias
RUN npm ci

# Generar Prisma Client
RUN npx prisma generate

# Copiar código fuente
COPY . .

# Build de la aplicación usando tsconfig.build.json explícitamente
RUN npx tsc -p tsconfig.build.json

# Mostrar archivos generados para debug
RUN ls -lR dist

# Cambiar propietario
RUN chown -R nestjs:nodejs /app

# Cambiar al usuario no-root
USER nestjs

# Exponer puerto
EXPOSE 3000

# Comando de inicio
ENTRYPOINT ["dumb-init", "--"]
CMD ["node", "dist/main.js"] 