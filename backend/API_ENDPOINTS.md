# 📚 Documentación de la API - Sistema de Talento Humano

## 🔐 Autenticación

### Login
```
POST /auth/login
Content-Type: application/json

{
  "email": "admin@empresa.com",
  "password": "admin123"
}
```

**Respuesta:**
```json
{
  "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "id": 1,
    "email": "admin@empresa.com",
    "name": "Administrador",
    "role": "ADMIN"
  }
}
```

## 👥 Usuarios

### Obtener todos los usuarios
```
GET /users
Authorization: Bearer [TOKEN]
```

### Obtener usuario específico
```
GET /users/:id
Authorization: Bearer [TOKEN]
```

### Crear usuario (Admin)
```
POST /users
Authorization: Bearer [TOKEN]
Content-Type: application/json

{
  "name": "Nuevo Usuario",
  "email": "usuario@empresa.com",
  "password": "password123",
  "role": "EMPLEADO"
}
```

### Actualizar usuario (Admin)
```
PATCH /users/:id
Authorization: Bearer [TOKEN]
Content-Type: application/json

{
  "name": "Nombre Actualizado",
  "role": "RECURSOS_HUMANOS"
}
```

### Eliminar usuario (Admin)
```
DELETE /users/:id
Authorization: Bearer [TOKEN]
```

## 📅 Eventos

### Obtener todos los eventos
```
GET /events
Authorization: Bearer [TOKEN]
```

### Obtener evento específico
```
GET /events/:id
Authorization: Bearer [TOKEN]
```

### Crear evento (Admin)
```
POST /events
Authorization: Bearer [TOKEN]
Content-Type: application/json

{
  "title": "Capacitación en React",
  "description": "Aprende React desde cero",
  "date": "2024-03-15T10:00:00.000Z",
  "duration": 120,
  "location": "Sala de Capacitación",
  "maxAttendees": 25
}
```

### Actualizar evento (Admin)
```
PATCH /events/:id
Authorization: Bearer [TOKEN]
Content-Type: application/json

{
  "title": "Título Actualizado",
  "duration": 180
}
```

### Activar/Desactivar evento (Admin)
```
PATCH /events/:id/toggle-status
Authorization: Bearer [TOKEN]
```

### Eliminar evento (Admin)
```
DELETE /events/:id
Authorization: Bearer [TOKEN]
```

## ✅ Asistencias

### Marcar asistencia
```
POST /attendances
Authorization: Bearer [TOKEN]
Content-Type: application/json

{
  "eventId": 1,
  "notes": "Asistencia confirmada"
}
```

### Obtener asistencias
```
GET /attendances
Authorization: Bearer [TOKEN]
```

### Obtener asistencia específica
```
GET /attendances/:id
Authorization: Bearer [TOKEN]
```

### Actualizar asistencia (RRHH/Admin)
```
PATCH /attendances/:id
Authorization: Bearer [TOKEN]
Content-Type: application/json

{
  "status": "CONFIRMED",
  "notes": "Asistencia confirmada por RRHH"
}
```

### Reporte de asistencias (RRHH/Admin)
```
GET /attendances/report?eventId=1
Authorization: Bearer [TOKEN]
```

### Eliminar asistencia (Admin)
```
DELETE /attendances/:id
Authorization: Bearer [TOKEN]
```

## 📊 Evaluaciones

### Crear evaluación
```
POST /evaluations
Authorization: Bearer [TOKEN]
Content-Type: application/json

{
  "eventId": 1,
  "feedback": "Excelente capacitación, aprendí mucho"
}
```

### Obtener evaluaciones
```
GET /evaluations
Authorization: Bearer [TOKEN]
```

### Obtener evaluación específica
```
GET /evaluations/:id
Authorization: Bearer [TOKEN]
```

### Calificar evaluación (RRHH/Admin)
```
PATCH /evaluations/:id/grade
Authorization: Bearer [TOKEN]
Content-Type: application/json

{
  "score": 9,
  "feedback": "Excelente evaluación, muy completa"
}
```

### Actualizar evaluación (Autor)
```
PATCH /evaluations/:id
Authorization: Bearer [TOKEN]
Content-Type: application/json

{
  "feedback": "Evaluación actualizada"
}
```

### Reporte de evaluaciones (RRHH/Admin)
```
GET /evaluations/report?eventId=1
Authorization: Bearer [TOKEN]
```

### Eliminar evaluación (Admin)
```
DELETE /evaluations/:id
Authorization: Bearer [TOKEN]
```

## 🔍 Health Check

### Verificar estado del sistema
```
GET /health
```

**Respuesta:**
```json
{
  "status": "ok",
  "timestamp": "2024-01-15T10:30:00.000Z",
  "uptime": 3600,
  "environment": "development"
}
```

## 🔑 Credenciales de Prueba

| Rol | Email | Password |
|-----|-------|----------|
| Administrador | admin@empresa.com | admin123 |
| Recursos Humanos | hr@empresa.com | hr123 |
| Empleado | empleado1@empresa.com | empleado123 |

## 📋 Estados y Enums

### UserRole
- `ADMIN` - Administrador
- `RECURSOS_HUMANOS` - Recursos Humanos
- `EMPLEADO` - Empleado

### EventStatus
- `ACTIVE` - Activo
- `INACTIVE` - Inactivo

### AttendanceStatus
- `PENDING` - Pendiente
- `CONFIRMED` - Confirmada
- `CANCELLED` - Cancelada

### EvaluationStatus
- `PENDING` - Pendiente
- `SUBMITTED` - Enviada
- `GRADED` - Calificada

## 🚨 Códigos de Error

- `400` - Bad Request (Datos inválidos)
- `401` - Unauthorized (Token inválido)
- `403` - Forbidden (Sin permisos)
- `404` - Not Found (Recurso no encontrado)
- `409` - Conflict (Recurso duplicado)
- `500` - Internal Server Error (Error del servidor) 