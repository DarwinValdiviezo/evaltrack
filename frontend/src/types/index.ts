// Tipos para el Sistema de Gestión de Talento Humano

export enum UserRole {
  ADMIN = 'ADMIN',
  RECURSOS_HUMANOS = 'RECURSOS_HUMANOS',
  EMPLEADO = 'EMPLEADO'
}

export enum EventStatus {
  ACTIVE = 'ACTIVE',
  INACTIVE = 'INACTIVE'
}

export enum AttendanceStatus {
  PENDING = 'PENDING',
  CONFIRMED = 'CONFIRMED',
  CANCELLED = 'CANCELLED'
}

export enum EvaluationStatus {
  PENDING = 'PENDING',
  SUBMITTED = 'SUBMITTED',
  GRADED = 'GRADED'
}

export interface User {
  id: number;
  name: string;
  email: string;
  role: UserRole;
  isActive: boolean;
  createdAt: string;
  updatedAt: string;
}

export interface Event {
  id: number;
  title: string;
  description?: string;
  date: string;
  duration: number;
  location?: string;
  maxAttendees?: number;
  status: EventStatus;
  isActive: boolean;
  createdAt: string;
  updatedAt: string;
  creator: {
    id: number;
    name: string;
    email: string;
  };
  _count?: {
    attendances: number;
  };
}

export interface Attendance {
  id: number;
  eventId: number;
  userId: number;
  status: AttendanceStatus;
  attendedAt?: string;
  notes?: string;
  createdAt: string;
  updatedAt: string;
  event: {
    id: number;
    title: string;
    date: string;
    location?: string;
    status: EventStatus;
  };
  user: {
    id: number;
    name: string;
    email: string;
    role: UserRole;
  };
}

export interface Evaluation {
  id: number;
  eventId: number;
  userId: number;
  graderId?: number;
  status: EvaluationStatus;
  score?: number;
  feedback?: string;
  submittedAt?: string;
  gradedAt?: string;
  createdAt: string;
  updatedAt: string;
  event: {
    id: number;
    title: string;
    date: string;
  };
  user: {
    id: number;
    name: string;
    email: string;
    role: UserRole;
  };
  grader?: {
    id: number;
    name: string;
    email: string;
  };
}

// DTOs para formularios
export interface LoginForm {
  email: string;
  password: string;
}

export interface CreateUserForm {
  name: string;
  email: string;
  password: string;
  role: UserRole;
}

export interface UpdateUserForm {
  name?: string;
  email?: string;
  password?: string;
  role?: UserRole;
}

export interface CreateEventForm {
  title: string;
  description?: string;
  date: string;
  duration: number;
  location?: string;
  maxAttendees?: number;
}

export interface UpdateEventForm {
  title?: string;
  description?: string;
  date?: string;
  duration?: number;
  location?: string;
  maxAttendees?: number;
}

export interface CreateAttendanceForm {
  eventId: number;
  notes?: string;
}

export interface UpdateAttendanceForm {
  status?: AttendanceStatus;
  notes?: string;
}

export interface CreateEvaluationForm {
  eventId: number;
  feedback?: string;
}

export interface GradeEvaluationForm {
  score: number;
  feedback?: string;
}

export interface UpdateEvaluationForm {
  feedback?: string;
}

// Respuestas de la API
export interface AuthResponse {
  access_token: string;
  user: User;
}

export interface ApiResponse<T> {
  data: T;
  message?: string;
}

export interface PaginatedResponse<T> {
  data: T[];
  total: number;
  page: number;
  limit: number;
  totalPages: number;
} 