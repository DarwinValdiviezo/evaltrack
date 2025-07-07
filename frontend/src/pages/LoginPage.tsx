import React, { useState } from 'react';
import { useForm } from 'react-hook-form';
import { Eye, EyeOff, Building } from 'lucide-react';
import { useAuth } from '../contexts/AuthContext';
import { toast } from 'react-hot-toast';
import type { LoginForm } from '../types';
import './LoginPage.css';

const LoginPage: React.FC = () => {
  const [showPassword, setShowPassword] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const { login } = useAuth();

  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm<LoginForm>();

  const onSubmit = async (data: LoginForm) => {
    setIsLoading(true);
    try {
      await login(data.email, data.password);
      toast.success('Inicio de sesión exitoso');
    } catch (error: any) {
      toast.error(error.response?.data?.message || 'Error al iniciar sesión');
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="login-container">
      <div className="login-background">
        <div className="login-overlay"></div>
      </div>
      
      <div className="login-content">
        <div className="login-card">
          <div className="login-header">
            <div className="login-logo">
              <Building className="logo-icon" />
            </div>
            <h1 className="login-title">Sistema de Gestión</h1>
            <h2 className="login-subtitle">Talento Humano</h2>
            <p className="login-description">
              Accede a tu cuenta para gestionar eventos, asistencias y evaluaciones
            </p>
          </div>

          <form className="login-form" onSubmit={handleSubmit(onSubmit)}>
            <div className="form-group">
              <label htmlFor="email" className="form-label">
                <i className="bi bi-envelope"></i>
                Correo Electrónico
              </label>
              <input
                {...register('email', {
                  required: 'El correo electrónico es requerido',
                  pattern: {
                    value: /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i,
                    message: 'Correo electrónico inválido',
                  },
                })}
                id="email"
                name="email"
                type="email"
                autoComplete="email"
                className={`form-input ${errors.email ? 'error' : ''}`}
                placeholder="tu@email.com"
              />
              {errors.email && (
                <span className="error-message">{errors.email.message}</span>
              )}
            </div>

            <div className="form-group">
              <label htmlFor="password" className="form-label">
                <i className="bi bi-lock"></i>
                Contraseña
              </label>
              <div className="password-input-container">
                <input
                  {...register('password', {
                    required: 'La contraseña es requerida',
                    minLength: {
                      value: 6,
                      message: 'La contraseña debe tener al menos 6 caracteres',
                    },
                  })}
                  id="password"
                  name="password"
                  type={showPassword ? 'text' : 'password'}
                  autoComplete="current-password"
                  className={`form-input ${errors.password ? 'error' : ''}`}
                  placeholder="••••••••"
                />
                <button
                  type="button"
                  className="password-toggle"
                  onClick={() => setShowPassword(!showPassword)}
                >
                  {showPassword ? <EyeOff size={20} /> : <Eye size={20} />}
                </button>
              </div>
              {errors.password && (
                <span className="error-message">{errors.password.message}</span>
              )}
            </div>

            <button
              type="submit"
              disabled={isLoading}
              className="login-button"
            >
              {isLoading ? (
                <div className="loading-spinner"></div>
              ) : (
                <>
                  <i className="bi bi-box-arrow-in-right"></i>
                  Iniciar Sesión
                </>
              )}
            </button>
          </form>

          <div className="login-footer">
            <div className="demo-credentials">
              <h4 className="demo-title">
                <i className="bi bi-info-circle"></i>
                Credenciales de Prueba
              </h4>
              <div className="demo-list">
                <div className="demo-item">
                  <span className="demo-role">Administrador:</span>
                  <span className="demo-email">admin@empresa.com</span>
                  <span className="demo-password">admin123</span>
                </div>
                <div className="demo-item">
                  <span className="demo-role">RRHH:</span>
                  <span className="demo-email">hr@empresa.com</span>
                  <span className="demo-password">hr123</span>
                </div>
                <div className="demo-item">
                  <span className="demo-role">Empleado:</span>
                  <span className="demo-email">empleado1@empresa.com</span>
                  <span className="demo-password">empleado123</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default LoginPage; 