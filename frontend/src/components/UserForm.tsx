import React from 'react';
import { useForm } from 'react-hook-form';
import { UserRole } from '../types';

interface UserFormProps {
  initialData?: any;
  onSubmit: (data: any) => void;
  onClose: () => void;
  loading?: boolean;
}

const roles = [
  { value: UserRole.ADMIN, label: 'Administrador' },
  { value: UserRole.RECURSOS_HUMANOS, label: 'Recursos Humanos' },
  { value: UserRole.EMPLEADO, label: 'Empleado' },
];

const UserForm: React.FC<UserFormProps> = ({ initialData, onSubmit, onClose, loading }) => {
  const { register, handleSubmit, formState: { errors } } = useForm({
    defaultValues: initialData ? {
      ...initialData,
      isActive: initialData.isActive ? 'true' : 'false',
    } : {
      name: '',
      email: '',
      role: UserRole.EMPLEADO,
      isActive: 'true',
      password: '',
    }
  });

  const handleFormSubmit = (data: any) => {
    data.isActive = data.isActive === 'true';
    onSubmit(data);
  };

  return (
    <div className="modal show d-block" tabIndex={-1}>
      <div className="modal-dialog">
        <form onSubmit={handleSubmit(handleFormSubmit)}>
          <div className="modal-content">
            <div className="modal-header">
              <h5 className="modal-title">{initialData ? 'Editar usuario' : 'Nuevo usuario'}</h5>
              <button type="button" className="btn-close" onClick={onClose}></button>
            </div>
            <div className="modal-body">
              <div className="mb-3">
                <label className="form-label">Nombre</label>
                <input className={`form-control ${errors.name ? 'is-invalid' : ''}`} {...register('name', { required: 'El nombre es obligatorio' })} />
                {errors.name && <div className="invalid-feedback">{errors.name.message as string}</div>}
              </div>
              <div className="mb-3">
                <label className="form-label">Email</label>
                <input type="email" className={`form-control ${errors.email ? 'is-invalid' : ''}`} {...register('email', { required: 'El email es obligatorio' })} />
                {errors.email && <div className="invalid-feedback">{errors.email.message as string}</div>}
              </div>
              <div className="mb-3">
                <label className="form-label">Rol</label>
                <select className="form-select" {...register('role', { required: true })}>
                  {roles.map(r => <option key={r.value} value={r.value}>{r.label}</option>)}
                </select>
              </div>
              <div className="mb-3">
                <label className="form-label">Estado</label>
                <select className="form-select" {...register('isActive', { required: true })}>
                  <option value="true">Activo</option>
                  <option value="false">Inactivo</option>
                </select>
              </div>
              {!initialData && (
                <div className="mb-3">
                  <label className="form-label">Contraseña</label>
                  <input type="password" className={`form-control ${errors.password ? 'is-invalid' : ''}`} {...register('password', { required: 'La contraseña es obligatoria', minLength: { value: 6, message: 'Mínimo 6 caracteres' } })} />
                  {errors.password && <div className="invalid-feedback">{errors.password.message as string}</div>}
                </div>
              )}
            </div>
            <div className="modal-footer">
              <button type="button" className="btn btn-secondary" onClick={onClose}>Cancelar</button>
              <button type="submit" className="btn btn-success" disabled={loading}>
                {loading ? 'Guardando...' : 'Guardar'}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  );
};

export default UserForm; 