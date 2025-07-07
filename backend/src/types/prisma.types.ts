// Tipos personalizados para Prisma
export enum UserRole {
  ADMIN = 'ADMIN',
  RECURSOS_HUMANOS = 'RECURSOS_HUMANOS',
  EMPLEADO = 'EMPLEADO',
}

export enum EventStatus {
  ACTIVE = 'ACTIVE',
  INACTIVE = 'INACTIVE',
}

export enum AttendanceStatus {
  PENDING = 'PENDING',
  CONFIRMED = 'CONFIRMED',
  CANCELLED = 'CANCELLED',
}

export enum EvaluationStatus {
  PENDING = 'PENDING',
  SUBMITTED = 'SUBMITTED',
  GRADED = 'GRADED',
} 