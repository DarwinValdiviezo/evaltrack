import React, { useState, useEffect } from 'react';
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { useNavigate, useParams } from 'react-router-dom';
import { userService } from '../lib/api';
import toast from 'react-hot-toast';
import './UsersPage.css';

const EditUserPage: React.FC = () => {
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const { id } = useParams<{ id: string }>();
  
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    role: '',
    isActive: true
  });
  
  const [errors, setErrors] = useState<Record<string, string>>({});

  // Consulta para obtener el usuario
  const { data: userData, isLoading } = useQuery({
    queryKey: ['user', id],
    queryFn: () => userService.getById(Number(id)),
    enabled: !!id,
  });

  // Mutación para actualizar usuario
  const updateMutation = useMutation({
    mutationFn: ({ id, data }: { id: number; data: any }) => userService.update(id, data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['users'] });
      queryClient.invalidateQueries({ queryKey: ['user', id] });
      toast.success('Usuario actualizado exitosamente', {
        duration: 4000,
        icon: '✅',
        style: {
          background: '#10b981',
          color: '#fff',
          fontWeight: '600',
        },
      });
      navigate('/users');
    },
    onError: (error: any) => {
      console.error('Error al actualizar usuario:', error);
      const errorMessage = error.response?.data?.message || 'Error al actualizar usuario';
      toast.error(errorMessage, {
        duration: 5000,
        icon: '❌',
        style: {
          background: '#ef4444',
          color: '#fff',
          fontWeight: '600',
        },
      });
    },
  });

  // Roles disponibles
  const roles = [
    { value: '', label: 'Seleccionar rol' },
    { value: 'ADMIN', label: 'Administrador' },
    { value: 'RECURSOS_HUMANOS', label: 'Talento Humano' },
    { value: 'EMPLEADO', label: 'Empleado' }
  ];

  // Cargar datos del usuario cuando se obtengan
  useEffect(() => {
    if (userData?.data) {
      const user = userData.data;
      setFormData({
        name: user.name || '',
        email: user.email || '',
        role: user.role || '',
        isActive: user.isActive !== undefined ? user.isActive : true
      });
    }
  }, [userData]);

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    const { name, value, type } = e.target;
    const checked = (e.target as HTMLInputElement).checked;
    
    setFormData(prev => ({
      ...prev,
      [name]: type === 'checkbox' ? checked : value
    }));
    
    // Limpiar error del campo
    if (errors[name]) {
      setErrors(prev => ({
        ...prev,
        [name]: ''
      }));
    }
  };

  const validateForm = () => {
    const newErrors: Record<string, string> = {};

    if (!formData.name.trim()) {
      newErrors.name = 'El nombre es requerido';
    }

    if (!formData.email.trim()) {
      newErrors.email = 'El email es requerido';
    } else if (!/\S+@\S+\.\S+/.test(formData.email)) {
      newErrors.email = 'El email no es válido';
    }

    if (!formData.role) {
      newErrors.role = 'El rol es requerido';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    
    if (validateForm() && id) {
      updateMutation.mutate({ id: Number(id), data: formData });
    } else {
      toast.error('Por favor, corrige los errores en el formulario', {
        duration: 4000,
        icon: '⚠️',
        style: {
          background: '#f59e0b',
          color: '#fff',
          fontWeight: '600',
        },
      });
    }
  };

  if (isLoading) {
    return (
      <div className="users-page">
        <div className="text-center">
          <div className="loading-spinner"></div>
          <p>Cargando usuario...</p>
        </div>
      </div>
    );
  }

  if (!userData?.data) {
    return (
      <div className="users-page">
        <div className="text-center">
          <p>Usuario no encontrado</p>
          <button 
            className="btn btn-primary"
            onClick={() => navigate('/users')}
          >
            Volver a usuarios
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="users-page">
      <div className="row justify-content-center">
        <div className="col-lg-6">
          <div className="card shadow-sm border-0">
            <div className="card-body">
              <h3 className="fw-bold mb-3">
                Asignar Rol a <span className="text-primary">{formData.name}</span>
              </h3>
              
              <form onSubmit={handleSubmit}>
                <div className="mb-3">
                  <label htmlFor="name" className="form-label">
                    Nombre de usuario
                  </label>
                  <input
                    type="text"
                    name="name"
                    id="name"
                    className={`form-control ${errors.name ? 'is-invalid' : ''}`}
                    value={formData.name}
                    onChange={handleInputChange}
                    placeholder="Ingrese el nombre completo"
                  />
                  {errors.name && (
                    <div className="invalid-feedback">{errors.name}</div>
                  )}
                </div>

                <div className="mb-3">
                  <label htmlFor="email" className="form-label">
                    Correo electrónico
                  </label>
                  <input
                    type="email"
                    name="email"
                    id="email"
                    className={`form-control ${errors.email ? 'is-invalid' : ''}`}
                    value={formData.email}
                    onChange={handleInputChange}
                    placeholder="usuario@ejemplo.com"
                  />
                  {errors.email && (
                    <div className="invalid-feedback">{errors.email}</div>
                  )}
                </div>

                <div className="mb-3">
                  <label htmlFor="role" className="form-label">
                    Rol
                  </label>
                  <select
                    name="role"
                    id="role"
                    className={`form-control ${errors.role ? 'is-invalid' : ''}`}
                    value={formData.role}
                    onChange={handleInputChange}
                  >
                    {roles.map(role => (
                      <option key={role.value} value={role.value}>
                        {role.label}
                      </option>
                    ))}
                  </select>
                  {errors.role && (
                    <div className="invalid-feedback">{errors.role}</div>
                  )}
                </div>

                <div className="mb-3">
                  <div className="form-check">
                    <input
                      type="checkbox"
                      name="isActive"
                      id="isActive"
                      className="form-check-input"
                      checked={formData.isActive}
                      onChange={handleInputChange}
                    />
                    <label htmlFor="isActive" className="form-check-label">
                      Usuario activo
                    </label>
                  </div>
                </div>

                <div className="d-flex justify-content-end gap-2">
                  <button 
                    type="submit" 
                    className="btn btn-success rounded-pill px-4"
                    disabled={updateMutation.isPending}
                  >
                    {updateMutation.isPending ? 'Guardando...' : 'Guardar'}
                  </button>
                  <button 
                    type="button"
                    className="btn btn-secondary rounded-pill px-4"
                    onClick={() => navigate('/users')}
                  >
                    Cancelar
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default EditUserPage; 