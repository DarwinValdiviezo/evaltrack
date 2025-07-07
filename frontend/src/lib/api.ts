import axios from 'axios';
import type { AuthResponse, User } from '../types';

const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:3000';

// Crear instancia de axios
const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Interceptor para agregar token a las peticiones
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Interceptor para manejar errores de respuesta
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Token expirado o inválido
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

// Servicios de autenticación
export const authService = {
  login: async (email: string, password: string): Promise<AuthResponse> => {
    const response = await api.post('/auth/login', { email, password });
    return response.data;
  },

  logout: () => {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
  },

  getCurrentUser: (): User | null => {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
  },

  setAuth: (token: string, user: User) => {
    localStorage.setItem('token', token);
    localStorage.setItem('user', JSON.stringify(user));
  },
};

// Servicios de usuarios
export const userService = {
  getAll: async () => {
    const response = await api.get('/users');
    return { data: response.data };
  },

  getById: async (id: number) => {
    const response = await api.get(`/users/${id}`);
    return { data: response.data };
  },

  create: async (userData: any) => {
    const response = await api.post('/users', userData);
    return { data: response.data };
  },

  update: async (id: number, userData: any) => {
    const response = await api.patch(`/users/${id}`, userData);
    return { data: response.data };
  },

  delete: async (id: number) => {
    const response = await api.delete(`/users/${id}`);
    return { data: response.data };
  },
};

// Servicios de eventos
export const eventService = {
  getAll: async () => {
    const response = await api.get('/events');
    return { data: response.data };
  },

  getById: async (id: number) => {
    const response = await api.get(`/events/${id}`);
    return { data: response.data };
  },

  create: async (eventData: any) => {
    const response = await api.post('/events', eventData);
    return { data: response.data };
  },

  update: async (id: number, eventData: any) => {
    const response = await api.patch(`/events/${id}`, eventData);
    return { data: response.data };
  },

  toggleStatus: async (id: number) => {
    const response = await api.patch(`/events/${id}/toggle-status`);
    return { data: response.data };
  },

  delete: async (id: number) => {
    const response = await api.delete(`/events/${id}`);
    return { data: response.data };
  },
};

// Servicios de asistencias
export const attendanceService = {
  getAll: async () => {
    const response = await api.get('/attendances');
    return { data: response.data };
  },

  getById: async (id: number) => {
    const response = await api.get(`/attendances/${id}`);
    return { data: response.data };
  },

  create: async (attendanceData: any) => {
    const response = await api.post('/attendances', attendanceData);
    return { data: response.data };
  },

  update: async (id: number, attendanceData: any) => {
    const response = await api.patch(`/attendances/${id}`, attendanceData);
    return { data: response.data };
  },

  delete: async (id: number) => {
    const response = await api.delete(`/attendances/${id}`);
    return { data: response.data };
  },

  getReport: async (eventId?: number) => {
    const params = eventId ? { eventId } : {};
    const response = await api.get('/attendances/report', { params });
    return { data: response.data };
  },

  confirm: async (id: number) => {
    const response = await api.patch(`/attendances/${id}/confirm`);
    return { data: response.data };
  },
};

// Servicios de evaluaciones
export const evaluationService = {
  getAll: async () => {
    const response = await api.get('/evaluations');
    return { data: response.data };
  },

  getById: async (id: number) => {
    const response = await api.get(`/evaluations/${id}`);
    return { data: response.data };
  },

  create: async (evaluationData: any) => {
    const response = await api.post('/evaluations', evaluationData);
    return { data: response.data };
  },

  update: async (id: number, evaluationData: any) => {
    const response = await api.patch(`/evaluations/${id}`, evaluationData);
    return { data: response.data };
  },

  grade: async (id: number, gradeData: any) => {
    const response = await api.patch(`/evaluations/${id}/grade`, gradeData);
    return { data: response.data };
  },

  answer: async (id: number, answerData: any) => {
    const response = await api.patch(`/evaluations/${id}/answer`, answerData);
    return { data: response.data };
  },

  delete: async (id: number) => {
    const response = await api.delete(`/evaluations/${id}`);
    return { data: response.data };
  },

  getReport: async (eventId?: number) => {
    const params = eventId ? { eventId } : {};
    const response = await api.get('/evaluations/report', { params });
    return { data: response.data };
  },
};

export default api; 