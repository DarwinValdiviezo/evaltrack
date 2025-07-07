import React, { createContext, useContext, useState, useEffect, type ReactNode } from 'react';
import { authService } from '../lib/api';
import type { User } from '../types';

interface AuthContextType {
  isAuthenticated: boolean;
  user: User | null;
  login: (email: string, password: string) => Promise<void>;
  logout: () => void;
  isLoading: boolean;
  // Funciones de permisos por rol
  canViewUsers: () => boolean;
  canManageUsers: () => boolean;
  canViewEvents: () => boolean;
  canManageEvents: () => boolean;
  canViewAttendances: () => boolean;
  canManageAttendances: () => boolean;
  canViewEvaluations: () => boolean;
  canManageEvaluations: () => boolean;
  canGradeEvaluations: () => boolean;
  canAnswerEvaluations: () => boolean;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};

interface AuthProviderProps {
  children: ReactNode;
}

export const AuthProvider: React.FC<AuthProviderProps> = ({ children }) => {
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [user, setUser] = useState<User | null>(null);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    const currentUser = authService.getCurrentUser();
    if (currentUser) {
      setUser(currentUser);
      setIsAuthenticated(true);
    }
    setIsLoading(false);
  }, []);

  const login = async (email: string, password: string) => {
    try {
      const response = await authService.login(email, password);
      authService.setAuth(response.access_token, response.user);
      setUser(response.user);
      setIsAuthenticated(true);
    } catch (error) {
      throw error;
    }
  };

  const logout = () => {
    authService.logout();
    setUser(null);
    setIsAuthenticated(false);
  };

  // Funciones de permisos por rol
  const canViewUsers = () => {
    return user?.role === 'ADMIN';
  };

  const canManageUsers = () => {
    return user?.role === 'ADMIN';
  };

  const canViewEvents = () => {
    return ['ADMIN', 'RECURSOS_HUMANOS', 'EMPLEADO'].includes(user?.role || '');
  };

  const canManageEvents = () => {
    return user?.role === 'ADMIN';
  };

  const canViewAttendances = () => {
    return ['ADMIN', 'RECURSOS_HUMANOS', 'EMPLEADO'].includes(user?.role || '');
  };

  const canManageAttendances = () => {
    return ['ADMIN', 'RECURSOS_HUMANOS'].includes(user?.role || '');
  };

  const canViewEvaluations = () => {
    return ['ADMIN', 'RECURSOS_HUMANOS', 'EMPLEADO'].includes(user?.role || '');
  };

  const canManageEvaluations = () => {
    return ['ADMIN', 'RECURSOS_HUMANOS'].includes(user?.role || '');
  };

  const canGradeEvaluations = () => {
    return ['ADMIN', 'RECURSOS_HUMANOS'].includes(user?.role || '');
  };

  const canAnswerEvaluations = () => {
    return user?.role === 'EMPLEADO';
  };

  const value: AuthContextType = {
    isAuthenticated,
    user,
    login,
    logout,
    isLoading,
    canViewUsers,
    canManageUsers,
    canViewEvents,
    canManageEvents,
    canViewAttendances,
    canManageAttendances,
    canViewEvaluations,
    canManageEvaluations,
    canGradeEvaluations,
    canAnswerEvaluations,
  };

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}; 