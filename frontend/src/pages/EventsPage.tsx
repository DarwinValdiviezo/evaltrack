import React, { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { eventService } from '../lib/api';
import { useNavigate } from 'react-router-dom';
import toast from 'react-hot-toast';
import './EventsPage.css';
import { useAuth } from '../contexts/AuthContext';

const EventsPage: React.FC = () => {
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const { canManageEvents, user } = useAuth();
  
  // Estados para filtros
  const [search, setSearch] = useState('');
  const [typeFilter, setTypeFilter] = useState('');
  const [statusFilter, setStatusFilter] = useState('');
  const [dateFilter, setDateFilter] = useState('');

  // Estados para el modal de eliminación
  const [showDeleteModal, setShowDeleteModal] = useState(false);
  const [eventToDelete, setEventToDelete] = useState<any>(null);

  // Consulta de eventos
  const { data, isLoading } = useQuery({
    queryKey: ['events'],
    queryFn: eventService.getAll,
  });

  // Mutación para eliminar evento
  const deleteMutation = useMutation({
    mutationFn: eventService.delete,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['events'] });
      setShowDeleteModal(false);
      setEventToDelete(null);
      toast.success('Evento eliminado exitosamente', {
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
      console.error('Error al eliminar evento:', error);
      const errorMessage = error.response?.data?.message || 'Error al eliminar evento';
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

  // Mutación para activar/desactivar evento
  const toggleStatusMutation = useMutation({
    mutationFn: eventService.toggleStatus,
    onSuccess: (data) => {
      queryClient.invalidateQueries({ queryKey: ['events'] });
      const isActive = data.data.isActive;
      toast.success(`Evento ${isActive ? 'activado' : 'desactivado'} exitosamente`, {
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
      console.error('Error al cambiar estado del evento:', error);
      const errorMessage = error.response?.data?.message || 'Error al cambiar estado del evento';
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

  const events = data?.data || [];

  // Filtrado de eventos según el rol
  const filteredEvents = events.filter((event: any) => {
    // Admin puede filtrar por estado
    if (user?.role === 'ADMIN') {
      const matchesStatus = !statusFilter || (statusFilter === 'ACTIVE' && event.isActive) || (statusFilter === 'INACTIVE' && !event.isActive);
      return matchesStatus;
    }
    // RRHH y Empleado solo ven eventos activos
    return event.isActive;
  });

  // Actualizar filtro de estado si no hay eventos que coincidan
  React.useEffect(() => {
    if (statusFilter && filteredEvents.length === 0 && events.length > 0) {
      // Si hay eventos pero ninguno coincide con el filtro de estado, limpiar el filtro
      setStatusFilter('');
      toast('No hay eventos con el estado seleccionado. Mostrando todos los eventos.', {
        duration: 3000,
        icon: 'ℹ️',
        style: {
          background: '#3b82f6',
          color: '#fff',
          fontWeight: '600',
        },
      });
    }
  }, [statusFilter, filteredEvents.length, events.length]);

  // Tipos de eventos disponibles
  const eventTypes = [
    { value: '', label: 'Todos los tipos' },
    { value: 'Capacitación', label: 'Capacitación' },
    { value: 'Taller', label: 'Taller' },
    { value: 'Conferencia', label: 'Conferencia' },
    { value: 'Reunión', label: 'Reunión' },
    { value: 'Otro', label: 'Otro' }
  ];

  const statuses = [
    { value: '', label: 'Todos los estados' },
    { value: 'ACTIVE', label: 'Activos' },
    { value: 'INACTIVE', label: 'Inactivos' }
  ];

  // Función para formatear fecha
  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('es-ES', {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit'
    });
  };

  // Función para formatear hora
  const formatTime = (dateString: string) => {
    return new Date(dateString).toLocaleTimeString('es-ES', {
      hour: '2-digit',
      minute: '2-digit'
    });
  };

  // Badge de estado
  const getStatusBadge = (isActive: boolean) => {
    return isActive
      ? <span className="badge badge-success">Activo</span>
      : <span className="badge badge-secondary">Inactivo</span>;
  };

  // Función para abrir modal de eliminación
  const handleDeleteClick = (event: any) => {
    setEventToDelete(event);
    setShowDeleteModal(true);
  };

  // Función para confirmar eliminación
  const confirmDelete = () => {
    if (eventToDelete) {
      deleteMutation.mutate(eventToDelete.id);
    }
  };

  // Función para cerrar modal
  const closeDeleteModal = () => {
    setShowDeleteModal(false);
    setEventToDelete(null);
  };

  // Función para activar/desactivar evento
  const handleToggleStatus = (eventId: number) => {
    toggleStatusMutation.mutate(eventId);
  };

  // Función para limpiar filtros
  const clearFilters = () => {
    setSearch('');
    setTypeFilter('');
    setStatusFilter('');
    setDateFilter('');
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
    <div className="events-page">
      {/* Header */}
      <div className="events-header">
        <div>
          <h1 className="events-title">Gestión de Eventos</h1>
          <p className="events-subtitle">Administra y visualiza los eventos de la empresa</p>
        </div>
        {canManageEvents() && (
          <button
            className="btn-new-event"
            onClick={() => navigate('/events/create')}
          >
            <i className="bi bi-calendar-plus"></i>
            Nuevo Evento
          </button>
        )}
      </div>

      {/* Filtros y búsqueda */}
      <div className="search-filters">
        <div className="row g-3">
          <div className="col-12 col-md-3">
            <input
              type="text"
              className="form-control"
              placeholder="Buscar por nombre o lugar..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
            />
          </div>
          <div className="col-12 col-md-2">
            <select 
              className="form-control"
              value={typeFilter}
              onChange={(e) => setTypeFilter(e.target.value)}
            >
              {eventTypes.map(type => (
                <option key={type.value} value={type.value}>{type.label}</option>
              ))}
            </select>
          </div>
          {user?.role === 'ADMIN' && (
            <div className="col-12 col-md-2">
              <select 
                className="form-control"
                value={statusFilter}
                onChange={(e) => setStatusFilter(e.target.value)}
              >
                {statuses.map(status => (
                  <option key={status.value} value={status.value}>{status.label}</option>
                ))}
              </select>
            </div>
          )}
          <div className="col-12 col-md-2">
            <input
              type="date"
              className="form-control"
              value={dateFilter}
              onChange={(e) => setDateFilter(e.target.value)}
            />
          </div>
          <div className="col-12 col-md-1">
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

      {/* Tabla de eventos */}
      <div className="table-container">
        <div className="table-responsive">
          <table className="table">
            <thead>
              <tr>
                <th>#</th>
                <th>Evento</th>
                <th>Fecha</th>
                <th>Duración</th>
                <th>Ubicación</th>
                <th>Estado</th>
                {canManageEvents() && <th>Acciones</th>}
              </tr>
            </thead>
            <tbody>
              {isLoading ? (
                <tr>
                  <td colSpan={canManageEvents() ? 7 : 6} className="loading-state">
                    <div className="loading-spinner"></div>
                    <p>Cargando eventos...</p>
                  </td>
                </tr>
              ) : filteredEvents.length === 0 ? (
                <tr>
                  <td colSpan={canManageEvents() ? 7 : 6} className="empty-state">
                    <i className="bi bi-calendar-x"></i>
                    <p>No se encontraron eventos.</p>
                  </td>
                </tr>
              ) : (
                filteredEvents.map((event: any, index: number) => (
                  <tr key={event.id}>
                    <td>{index + 1}</td>
                    <td>
                      <div className="event-info">
                        <span className="event-title">{event.title}</span>
                        <span className="event-description">{event.description}</span>
                      </div>
                    </td>
                    <td>{new Date(event.date).toLocaleDateString('es-ES')}</td>
                    <td>{event.duration} min</td>
                    <td>{event.location || '-'}</td>
                    <td>{getStatusBadge(event.isActive)}</td>
                    {canManageEvents() && (
                      <td>
                        <button
                          className="btn-action btn-edit"
                          onClick={() => navigate(`/events/edit/${event.id}`)}
                          title="Editar evento"
                        >
                          <i className="bi bi-pencil"></i>
                        </button>
                        <button
                          className="btn-action btn-toggle"
                          onClick={() => handleToggleStatus(event.id)}
                          disabled={toggleStatusMutation.isPending}
                          title={event.isActive ? 'Desactivar' : 'Activar'}
                        >
                          <i className={`bi ${event.isActive ? 'bi-eye-slash' : 'bi-eye'}`}></i>
                        </button>
                        <button
                          className="btn-action btn-delete"
                          onClick={() => handleDeleteClick(event)}
                          disabled={deleteMutation.isPending}
                          title="Eliminar evento"
                        >
                          <i className="bi bi-trash"></i>
                        </button>
                      </td>
                    )}
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* Paginación */}
      {filteredEvents.length > 0 && (
        <div className="pagination-container">
          <p className="text-muted">
            Mostrando {filteredEvents.length} de {events.length} eventos
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
                ¿Estás seguro de que quieres eliminar el evento{' '}
                <strong>{eventToDelete?.title}</strong>?
              </p>
              <p className="text-muted">
                Esta acción no se puede deshacer y se eliminarán todos los datos asociados al evento.
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
                    Eliminar Evento
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

export default EventsPage; 