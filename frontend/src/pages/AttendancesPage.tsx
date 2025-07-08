import React, { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { attendanceService, eventService } from '../lib/api';
import { useNavigate } from 'react-router-dom';
import toast from 'react-hot-toast';
import './AttendancesPage.css';
import { useAuth } from '../contexts/AuthContext';

const AttendancesPage: React.FC = () => {
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const { canManageAttendances, user } = useAuth();

  // Filtros
  const [search, setSearch] = useState('');
  const [dateFilter, setDateFilter] = useState('');
  const [statusFilter, setStatusFilter] = useState('');

  // Modal de eliminación
  const [showDeleteModal, setShowDeleteModal] = useState(false);
  const [attendanceToDelete, setAttendanceToDelete] = useState<any>(null);

  // Consultas
  const { data, isLoading } = useQuery({
    queryKey: ['attendances'],
    queryFn: attendanceService.getAll,
  });

  // Mutación para eliminar asistencia
  const deleteMutation = useMutation({
    mutationFn: attendanceService.delete,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['attendances'] });
      setShowDeleteModal(false);
      setAttendanceToDelete(null);
      toast.success('Asistencia eliminada exitosamente', {
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
      const errorMessage = error.response?.data?.message || 'Error al eliminar asistencia';
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

  // Mutación para confirmar asistencia
  const confirmMutation = useMutation({
    mutationFn: (id: number) => attendanceService.confirm(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['attendances'] });
      queryClient.invalidateQueries({ queryKey: ['evaluations'] }); // <-- refresca evaluaciones
      toast.success('Asistencia confirmada', {
        duration: 4000,
        icon: '✅',
        style: { background: '#10b981', color: '#fff', fontWeight: '600' },
      });
    },
    onError: (error: any) => {
      const errorMessage = error.response?.data?.message || 'Error al confirmar asistencia';
      toast.error(errorMessage, {
        duration: 5000,
        icon: '❌',
        style: { background: '#ef4444', color: '#fff', fontWeight: '600' },
      });
    },
  });

  // Consulta de eventos activos (solo para empleados)
  const { data: eventsData } = useQuery({
    queryKey: ['events'],
    queryFn: eventService.getAll,
    enabled: user?.role === 'EMPLEADO',
  });
  const activeEvents = (eventsData?.data || []).filter((ev: any) => ev.isActive);

  // Crear asistencia para evento activo sin asistencia
  const createAttendanceMutation = useMutation({
    mutationFn: (eventId: number) => attendanceService.create({ eventId }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['attendances'] });
      toast.success('Asistencia creada', {
        duration: 4000,
        icon: '✅',
        style: { background: '#10b981', color: '#fff', fontWeight: '600' },
      });
    },
    onError: (error: any) => {
      const errorMessage = error.response?.data?.message || 'Error al crear asistencia';
      toast.error(errorMessage, {
        duration: 5000,
        icon: '❌',
        style: { background: '#ef4444', color: '#fff', fontWeight: '600' },
      });
    },
  });

  const attendances = data?.data || [];

  // Filtrado de asistencias
  const filteredAttendances = attendances.filter((att: any) => {
    const matchesSearch =
      att.user?.name?.toLowerCase().includes(search.toLowerCase()) ||
      att.event?.title?.toLowerCase().includes(search.toLowerCase()) ||
      att.status?.toLowerCase().includes(search.toLowerCase());
    const matchesDate = !dateFilter || (att.attendedAt && att.attendedAt.startsWith(dateFilter));
    const matchesStatus = !statusFilter || att.status === statusFilter;
    return matchesSearch && matchesDate && matchesStatus;
  });

  const virtualRows = user?.role === 'EMPLEADO' ? activeEvents.filter((ev: any) => !attendances.some((att: any) => att.eventId === ev.id)) : [];

  // Estados posibles
  const statusOptions = [
    { value: '', label: 'Todos los estados' },
    { value: 'PENDING', label: 'Pendiente' },
    { value: 'CONFIRMED', label: 'Confirmada' },
    { value: 'CANCELLED', label: 'Cancelada' },
  ];

  // Modal de eliminar
  const handleDeleteClick = (attendance: any) => {
    setAttendanceToDelete(attendance);
    setShowDeleteModal(true);
  };
  const confirmDelete = () => {
    if (attendanceToDelete) {
      deleteMutation.mutate(attendanceToDelete.id);
    }
  };
  const closeDeleteModal = () => {
    setShowDeleteModal(false);
    setAttendanceToDelete(null);
  };

  // Limpiar filtros
  const clearFilters = () => {
    setSearch('');
    setDateFilter('');
    setStatusFilter('');
    toast('Filtros limpiados', {
      duration: 2000,
      icon: '🧹',
      style: {
        background: '#36b9cc',
        color: '#fff',
        fontWeight: '600',
      },
    });
  };

  // Badge de estado
  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'CONFIRMED':
        return <span className="badge badge-success">Confirmada</span>;
      case 'PENDING':
        return <span className="badge badge-warning">Pendiente</span>;
      case 'CANCELLED':
        return <span className="badge badge-danger">Cancelada</span>;
      default:
        return <span className="badge badge-secondary">{status}</span>;
    }
  };

  return (
    <div className="attendances-page">
      {/* Header */}
      <div className="attendances-header">
        <div>
          <h1 className="attendances-title">Gestión de Asistencias</h1>
          <p className="attendances-subtitle">
            {user?.role === 'EMPLEADO' 
              ? 'Visualiza y confirma tus asistencias a eventos' 
              : 'Administra y visualiza las asistencias del personal'
            }
          </p>
        </div>
        {canManageAttendances() && (
          <button
            className="btn-new-attendance"
            onClick={() => navigate('/attendances/create')}
          >
            <i className="bi bi-plus-circle"></i>
            Nueva Asistencia
          </button>
        )}
      </div>

      {/* Filtros y búsqueda */}
      <div className="search-filters">
        <div className="row g-3">
          <div className="col-12 col-md-4">
            <input
              type="text"
              className="form-control"
              placeholder="Buscar por empleado, evento o estado..."
              value={search}
              onChange={e => setSearch(e.target.value)}
            />
          </div>
          <div className="col-12 col-md-3">
            <input
              type="date"
              className="form-control"
              value={dateFilter}
              onChange={e => setDateFilter(e.target.value)}
            />
          </div>
          <div className="col-12 col-md-2">
            <select
              className="form-control"
              value={statusFilter}
              onChange={e => setStatusFilter(e.target.value)}
            >
              {statusOptions.map(opt => (
                <option key={opt.value} value={opt.value}>{opt.label}</option>
              ))}
            </select>
          </div>
          <div className="col-12 col-md-1">
            <button className="btn-search w-100">
              <i className="bi bi-search"></i> Buscar
            </button>
          </div>
          <div className="col-12 col-md-2">
            <button className="btn-clear w-100" onClick={clearFilters}>
              Limpiar
            </button>
          </div>
        </div>
      </div>

      {/* Tabla de asistencias */}
      <div className="table-container">
        <div className="table-responsive">
          <table className="table">
            <thead>
              <tr>
                <th>#</th>
                <th>Empleado</th>
                <th>Evento</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Notas</th>
                {(canManageAttendances() || user?.role === 'EMPLEADO') && <th>Acciones</th>}
              </tr>
            </thead>
            <tbody>
              {isLoading ? (
                <tr>
                  <td colSpan={canManageAttendances() ? 7 : 6} className="loading-state">
                    <div className="loading-spinner"></div>
                    <p>Cargando asistencias...</p>
                  </td>
                </tr>
              ) : filteredAttendances.length === 0 && virtualRows.length === 0 ? (
                <tr>
                  <td colSpan={canManageAttendances() ? 7 : 6} className="empty-state">
                    <i className="bi bi-calendar-x"></i>
                    <p>No se encontraron asistencias.</p>
                  </td>
                </tr>
              ) : (
                <>
                  {filteredAttendances.map((attendance: any, index: number) => (
                    <tr key={attendance.id}>
                      <td>{index + 1}</td>
                      <td>
                        <div className="attendance-info">
                          <span className="attendance-user">{attendance.user?.name}</span>
                          <span className="attendance-email">{attendance.user?.email}</span>
                        </div>
                      </td>
                      <td>{attendance.event?.title}</td>
                      <td>{new Date(attendance.event?.date).toLocaleDateString('es-ES')}</td>
                      <td>
                        {user?.role === 'EMPLEADO' && attendance.status === 'CONFIRMED' && (
                          <span className="badge badge-success">Asistido</span>
                        )}
                        {getStatusBadge(attendance.status)}
                      </td>
                      <td>{attendance.notes || '-'}</td>
                      {(canManageAttendances() || user?.role === 'EMPLEADO') && (
                        <td>
                          {canManageAttendances() && (
                            <>
                              <button
                                className="btn-action btn-view"
                                onClick={() => navigate(`/attendances/${attendance.id}`)}
                                title="Ver detalle"
                              >
                                <i className="bi bi-eye"></i>
                              </button>
                              <button
                                className="btn-action btn-edit"
                                onClick={() => navigate(`/attendances/edit/${attendance.id}`)}
                                title="Editar asistencia"
                              >
                                <i className="bi bi-pencil"></i>
                              </button>
                              <button
                                className="btn-action btn-delete"
                                onClick={() => handleDeleteClick(attendance)}
                                disabled={deleteMutation.isPending}
                                title="Eliminar asistencia"
                              >
                                <i className="bi bi-trash"></i>
                              </button>
                            </>
                          )}
                          {user?.role === 'EMPLEADO' && (
                            <button
                              className="btn-action btn-confirm"
                              onClick={() => confirmMutation.mutate(attendance.id)}
                              disabled={confirmMutation.isPending}
                              title="Confirmar asistencia"
                            >
                              <i className="bi bi-check-circle"></i> Confirmar
                            </button>
                          )}
                        </td>
                      )}
                    </tr>
                  ))}
                  {virtualRows.map((ev: any) => (
                    <tr key={`virtual-${ev.id}`} className="virtual-row">
                      <td>-</td>
                      <td>
                        <div className="attendance-info">
                          <span className="attendance-user">{user?.name}</span>
                          <span className="attendance-email">{user?.email}</span>
                        </div>
                      </td>
                      <td>{ev.title}</td>
                      <td>{new Date(ev.date).toLocaleDateString('es-ES')}</td>
                      <td><span className="badge badge-secondary">Sin asistencia</span></td>
                      <td>-</td>
                      <td>
                        <button
                          className="btn-action btn-confirm"
                          onClick={() => createAttendanceMutation.mutate(ev.id)}
                          disabled={createAttendanceMutation.isPending}
                          title="Registrar asistencia"
                        >
                          <i className="bi bi-check-circle"></i> Registrar
                        </button>
                      </td>
                    </tr>
                  ))}
                </>
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* Paginación */}
      {filteredAttendances.length > 0 && (
        <div className="pagination-container">
          <p className="text-muted">
            Mostrando {filteredAttendances.length} de {attendances.length} asistencias
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
                ¿Estás seguro de que quieres eliminar la asistencia de{' '}
                <strong>{attendanceToDelete?.user?.name}</strong> para el evento{' '}
                <strong>{attendanceToDelete?.event?.title}</strong>?
              </p>
              <p className="text-muted">
                Esta acción no se puede deshacer y se eliminarán todos los datos asociados a la asistencia.
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
                    Eliminar Asistencia
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

export default AttendancesPage; 