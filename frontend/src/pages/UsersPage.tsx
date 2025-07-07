import React, { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { userService } from '../lib/api';
import { useNavigate } from 'react-router-dom';
import toast from 'react-hot-toast';
import './UsersPage.css';

const UsersPage: React.FC = () => {
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  
  // Estados para filtros
  const [search, setSearch] = useState('');
  const [roleFilter, setRoleFilter] = useState('');
  const [statusFilter, setStatusFilter] = useState('');

  // Estados para el modal de eliminación
  const [showDeleteModal, setShowDeleteModal] = useState(false);
  const [userToDelete, setUserToDelete] = useState<any>(null);

  // Consulta de usuarios
  const { data, isLoading } = useQuery({
    queryKey: ['users'],
    queryFn: userService.getAll,
  });

  // Mutación para eliminar usuario
  const deleteMutation = useMutation({
    mutationFn: userService.delete,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['users'] });
      setShowDeleteModal(false);
      setUserToDelete(null);
      toast.success('Usuario eliminado exitosamente', {
        duration: 4000,
        icon: '✅',
        style: {
          background: '#10b981',
          color: '#fff',
          fontWeight: '600',
        },
      });
    },
    onError: (error: any) => {
      console.error('Error al eliminar usuario:', error);
      const errorMessage = error.response?.data?.message || 'Error al eliminar usuario';
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

  const users = data?.data || [];

  // Filtrado de usuarios
  const filteredUsers = users.filter((user: any) => {
    const matchesSearch = 
      user.name?.toLowerCase().includes(search.toLowerCase()) ||
      user.email?.toLowerCase().includes(search.toLowerCase());
    
    const matchesRole = !roleFilter || user.role === roleFilter;
    const matchesStatus = !statusFilter || 
      (statusFilter === 'active' && user.isActive) ||
      (statusFilter === 'inactive' && !user.isActive);
    
    return matchesSearch && matchesRole && matchesStatus;
  });

  // Roles disponibles (ajustar según tu backend)
  const roles = ['ADMIN', 'USER', 'MANAGER'];
  const statuses = [
    { value: '', label: 'Todos los estados' },
    { value: 'active', label: 'Activos' },
    { value: 'inactive', label: 'Inactivos' }
  ];

  // Función para obtener iniciales del nombre
  const getInitials = (name: string) => {
    return name
      .split(' ')
      .map(word => word.charAt(0))
      .join('')
      .toUpperCase()
      .slice(0, 2);
  };

  // Función para abrir modal de eliminación
  const handleDeleteClick = (user: any) => {
    setUserToDelete(user);
    setShowDeleteModal(true);
  };

  // Función para confirmar eliminación
  const confirmDelete = () => {
    if (userToDelete) {
      deleteMutation.mutate(userToDelete.id);
    }
  };

  // Función para cerrar modal
  const closeDeleteModal = () => {
    setShowDeleteModal(false);
    setUserToDelete(null);
  };

  // Función para limpiar filtros
  const clearFilters = () => {
    setSearch('');
    setRoleFilter('');
    setStatusFilter('');
    toast.success('Filtros limpiados', {
      duration: 2000,
      icon: '🧹',
      style: {
        background: '#3b82f6',
        color: '#fff',
        fontWeight: '600',
      },
    });
  };

  return (
    <div className="users-page">
      {/* Header */}
      <div className="users-header">
        <div>
          <h1 className="users-title">Gestión de Usuarios</h1>
          <p className="users-subtitle">Administra los usuarios y sus roles</p>
        </div>
        <button 
          className="btn-new-user"
          onClick={() => navigate('/users/create')}
        >
          <i className="bi bi-person-plus"></i>
          Nuevo Usuario
        </button>
      </div>

      {/* Filtros y búsqueda */}
      <div className="search-filters">
        <div className="row g-3">
          <div className="col-12 col-md-4">
            <input
              type="text"
              className="form-control"
              placeholder="Buscar por nombre o email..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
            />
          </div>
          <div className="col-12 col-md-3">
            <select 
              className="form-control"
              value={roleFilter}
              onChange={(e) => setRoleFilter(e.target.value)}
            >
              <option value="">Todos los roles</option>
              {roles.map(role => (
                <option key={role} value={role}>{role}</option>
              ))}
            </select>
          </div>
          <div className="col-12 col-md-2">
            <button className="btn-search w-100">
              <i className="bi bi-search"></i> Buscar
            </button>
          </div>
          <div className="col-12 col-md-2">
            <button 
              className="btn-clear w-100"
              onClick={clearFilters}
            >
              Limpiar
            </button>
          </div>
        </div>
      </div>

      {/* Tabla de usuarios */}
      <div className="table-container">
        <div className="table-responsive">
          <table className="table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              {isLoading ? (
                <tr>
                  <td colSpan={6} className="loading-state">
                    <div className="loading-spinner"></div>
                    <p>Cargando usuarios...</p>
                  </td>
                </tr>
              ) : filteredUsers.length === 0 ? (
                <tr>
                  <td colSpan={6} className="empty-state">
                    <i className="bi bi-people"></i>
                    <p>No se encontraron usuarios.</p>
                  </td>
                </tr>
              ) : (
                filteredUsers.map((user: any) => (
                  <tr key={user.id}>
                    <td>{user.id}</td>
                    <td>
                      <div className="user-info">
                        <div className="user-avatar">
                          {getInitials(user.name || user.email)}
                        </div>
                        <span className="user-name">{user.name || user.email}</span>
                      </div>
                    </td>
                    <td>{user.email}</td>
                    <td>
                      <span className="badge badge-primary">
                        {user.role || 'Sin rol'}
                      </span>
                    </td>
                    <td>
                      {user.isActive ? (
                        <span className="badge badge-success">Activo</span>
                      ) : (
                        <span className="badge badge-secondary">Inactivo</span>
                      )}
                    </td>
                    <td>
                      <button 
                        className="btn-action btn-edit"
                        onClick={() => navigate(`/users/edit/${user.id}`)}
                      >
                        <i className="bi bi-pencil"></i> Editar
                      </button>
                      <button 
                        className="btn-action btn-delete"
                        onClick={() => handleDeleteClick(user)}
                        disabled={deleteMutation.isPending}
                      >
                        <i className="bi bi-trash"></i> Eliminar
                      </button>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* Paginación (opcional) */}
      {filteredUsers.length > 0 && (
        <div className="pagination-container">
          <p className="text-muted">
            Mostrando {filteredUsers.length} de {users.length} usuarios
          </p>
        </div>
      )}

      {/* Modal de confirmación de eliminación */}
      {showDeleteModal && (
        <div className="modal-overlay">
          <div className="modal-content">
            <div className="modal-header">
              <h5 className="modal-title">
                <i className="bi bi-exclamation-triangle text-warning"></i>
                Confirmar Eliminación
              </h5>
              <button 
                type="button" 
                className="modal-close"
                onClick={closeDeleteModal}
              >
                <i className="bi bi-x-lg"></i>
              </button>
            </div>
            <div className="modal-body">
              <p>
                ¿Estás seguro de que quieres eliminar al usuario{' '}
                <strong>{userToDelete?.name || userToDelete?.email}</strong>?
              </p>
              <p className="text-muted">
                Esta acción no se puede deshacer y se eliminarán todos los datos asociados al usuario.
              </p>
            </div>
            <div className="modal-footer">
              <button 
                type="button" 
                className="btn btn-secondary"
                onClick={closeDeleteModal}
                disabled={deleteMutation.isPending}
              >
                Cancelar
              </button>
              <button 
                type="button" 
                className="btn btn-danger"
                onClick={confirmDelete}
                disabled={deleteMutation.isPending}
              >
                {deleteMutation.isPending ? (
                  <>
                    <div className="spinner-border spinner-border-sm me-2" role="status"></div>
                    Eliminando...
                  </>
                ) : (
                  <>
                    <i className="bi bi-trash me-2"></i>
                    Eliminar Usuario
                  </>
                )}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default UsersPage; 