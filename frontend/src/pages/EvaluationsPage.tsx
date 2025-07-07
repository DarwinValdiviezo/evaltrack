import React, { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { evaluationService, userService } from '../lib/api';
import { useNavigate } from 'react-router-dom';
import toast from 'react-hot-toast';
import './EvaluationsPage.css';
import { useAuth } from '../contexts/AuthContext';

const EvaluationsPage: React.FC = () => {
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const { canManageEvaluations, canGradeEvaluations, canAnswerEvaluations, user } = useAuth();

  // Filtros
  const [search, setSearch] = useState('');
  const [statusFilter, setStatusFilter] = useState('');

  // Modal de eliminación
  const [showDeleteModal, setShowDeleteModal] = useState(false);
  const [evaluationToDelete, setEvaluationToDelete] = useState<any>(null);

  // Consultas
  const { data, isLoading } = useQuery({
    queryKey: ['evaluations'],
    queryFn: evaluationService.getAll,
  });

  // Mutación para eliminar evaluación
  const deleteMutation = useMutation({
    mutationFn: evaluationService.delete,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['evaluations'] });
      setShowDeleteModal(false);
      setEvaluationToDelete(null);
      toast.success('Evaluación eliminada exitosamente', {
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
      const errorMessage = error.response?.data?.message || 'Error al eliminar evaluación';
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

  const evaluations = data?.data || [];

  // Filtrado de evaluaciones
  const filteredEvaluations = evaluations.filter((eva: any) => {
    const matchesSearch =
      eva.user?.name?.toLowerCase().includes(search.toLowerCase()) ||
      eva.titulo?.toLowerCase().includes(search.toLowerCase()) ||
      eva.status?.toLowerCase().includes(search.toLowerCase());
    const matchesStatus = !statusFilter || eva.status === statusFilter;
    return matchesSearch && matchesStatus;
  });

  // Estados posibles
  const statusOptions = [
    { value: '', label: 'Todos los estados' },
    { value: 'Pendiente', label: 'Pendiente' },
    { value: 'Disponible', label: 'Disponible' },
    { value: 'Completada', label: 'Completada' },
    { value: 'Calificada', label: 'Calificada' },
  ];

  // Modal de eliminar
  const handleDeleteClick = (eva: any) => {
    setEvaluationToDelete(eva);
    setShowDeleteModal(true);
  };
  const confirmDelete = () => {
    if (evaluationToDelete) {
      deleteMutation.mutate(evaluationToDelete.id);
    }
  };
  const closeDeleteModal = () => {
    setShowDeleteModal(false);
    setEvaluationToDelete(null);
  };

  // Limpiar filtros
  const clearFilters = () => {
    setSearch('');
    setStatusFilter('');
    toast('Filtros limpiados', {
      duration: 2000,
      icon: '🧹',
      style: {
        background: '#4e73df',
        color: '#fff',
        fontWeight: '600',
      },
    });
  };

  // Badge de estado
  const getStatusBadge = (status: string, nota?: number) => {
    // Mapeo de status backend -> frontend
    let displayStatus = status;
    if (status === 'PENDING') displayStatus = 'Pendiente';
    if (status === 'SUBMITTED') displayStatus = 'Completada';
    if (status === 'GRADED') displayStatus = 'Calificada';
    switch (displayStatus) {
      case 'Pendiente':
        return <span className="badge badge-warning">Pendiente</span>;
      case 'Completada':
        return <span className="badge badge-info">Completada</span>;
      case 'Calificada':
        return <span className="badge badge-success">Calificada{nota ? ` (${nota}/10)` : ''}</span>;
      default:
        return <span className="badge badge-secondary">{displayStatus}</span>;
    }
  };

  return (
    <div className="evaluations-page">
      {/* Header */}
      <div className="evaluations-header">
        <div>
          <h1 className="evaluations-title">Gestión de Evaluaciones</h1>
          <p className="evaluations-subtitle">
            {user?.role === 'EMPLEADO' 
              ? 'Visualiza y responde tus evaluaciones' 
              : 'Administra y visualiza las evaluaciones del personal'
            }
          </p>
        </div>
        {canManageEvaluations() && (
          <button
            className="btn-new-evaluation"
            onClick={() => navigate('/evaluations/create')}
          >
            <i className="bi bi-clipboard-plus"></i>
            Nueva Evaluación
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
              placeholder="Buscar por empleado, estado o título..."
              value={search}
              onChange={e => setSearch(e.target.value)}
            />
          </div>
          <div className="col-12 col-md-3">
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
          <div className="col-12 col-md-2">
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

      {/* Tabla de evaluaciones */}
      <div className="table-container">
        <div className="table-responsive">
          <table className="table">
            <thead>
              <tr>
                <th>#</th>
                <th>Empleado</th>
                <th>Título</th>
                <th>Fecha</th>
                <th>Preguntas</th>
                <th>Tipo</th>
                <th>Estado</th>
                <th>Nota</th>
                {(canManageEvaluations() || canGradeEvaluations() || canAnswerEvaluations()) && <th>Acciones</th>}
              </tr>
            </thead>
            <tbody>
              {isLoading ? (
                <tr>
                  <td colSpan={(canManageEvaluations() || canGradeEvaluations() || canAnswerEvaluations()) ? 7 : 6} className="loading-state">
                    <div className="loading-spinner"></div>
                    <p>Cargando evaluaciones...</p>
                  </td>
                </tr>
              ) : filteredEvaluations.length === 0 ? (
                <tr>
                  <td colSpan={(canManageEvaluations() || canGradeEvaluations() || canAnswerEvaluations()) ? 7 : 6} className="empty-state">
                    <i className="bi bi-clipboard-x"></i>
                    <p>No se encontraron evaluaciones.</p>
                  </td>
                </tr>
              ) : (
                filteredEvaluations.map((eva: any, index: number) => (
                  <tr key={eva.id}>
                    <td>{index + 1}</td>
                    <td>
                      <div className="evaluation-info">
                        <span className="evaluation-user">{eva.user?.name || '-'}</span>
                        <span className="evaluation-title">{eva.user?.email || ''}</span>
                      </div>
                    </td>
                    <td>{eva.titulo}</td>
                    <td>{eva.event?.date ? new Date(eva.event.date).toLocaleDateString('es-ES') : '-'}</td>
                    <td>
                      {eva.event?.questions && eva.event.questions.length > 0 ? (
                        <span className="badge badge-info">Con preguntas</span>
                      ) : (
                        <span className="badge badge-secondary">Sin preguntas</span>
                      )}
                    </td>
                    <td>{eva.event?.type || '-'}</td>
                    <td>{eva.event?.date ? new Date(eva.event.date).toLocaleDateString('es-ES') : '-'}</td>
                    <td>{getStatusBadge(eva.status, eva.nota)} <span style={{fontSize:'10px',color:'#888'}}>({eva.status})</span></td>
                    <td>
                      {eva.status === 'GRADED' ? (
                        <span className="badge badge-success">{eva.nota ?? '-'}</span>
                      ) : (
                        <span className="badge badge-secondary">-</span>
                      )}
                    </td>
                    {(canManageEvaluations() || canGradeEvaluations() || (user?.role === 'EMPLEADO' && eva.user?.id === user.id)) && (
                      <td>
                        {user?.role === 'EMPLEADO' && eva.user?.id === user.id && eva.status === 'PENDING' && eva.event?.questions?.length > 0 && (
                          <button
                            className="btn-action btn-answer"
                            onClick={() => navigate(`/evaluations/${eva.id}`)}
                            title="Realizar evaluación"
                          >
                            <i className="bi bi-chat-dots"></i> Realizar evaluación
                          </button>
                        )}
                        {user?.role === 'EMPLEADO' && eva.user?.id === user.id && eva.status !== 'PENDING' && (
                          <button
                            className="btn-action btn-view"
                            onClick={() => navigate(`/evaluations/${eva.id}`)}
                            title="Ver mis respuestas"
                          >
                            <i className="bi bi-eye"></i> Ver
                          </button>
                        )}
                        {user?.role === 'EMPLEADO' && eva.user?.id === user.id && eva.status === 'GRADED' && (
                          <span className="badge badge-success ms-2">Nota: <b>{eva.nota ?? '-'}</b></span>
                        )}
                        {canManageEvaluations() && (
                          <>
                            <button
                              className="btn-action btn-view"
                              onClick={() => navigate(`/evaluations/${eva.id}`)}
                              title="Ver detalle"
                            >
                              <i className="bi bi-eye"></i>
                            </button>
                            <button
                              className="btn-action btn-edit"
                              onClick={() => navigate(`/evaluations/edit/${eva.id}`)}
                              title="Editar evaluación"
                            >
                              <i className="bi bi-pencil"></i>
                            </button>
                            <button
                              className="btn-action btn-delete"
                              onClick={() => handleDeleteClick(eva)}
                              disabled={deleteMutation.isPending}
                              title="Eliminar evaluación"
                            >
                              <i className="bi bi-trash"></i>
                            </button>
                          </>
                        )}
                        {canGradeEvaluations() && eva.status === 'SUBMITTED' && (
                          <button
                            className="btn-action btn-grade"
                            onClick={() => navigate(`/evaluations/grade/${eva.id}`)}
                            title="Calificar evaluación"
                          >
                            <i className="bi bi-star"></i> Calificar
                          </button>
                        )}
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
      {filteredEvaluations.length > 0 && (
        <div className="pagination-container">
          <p className="text-muted">
            Mostrando {filteredEvaluations.length} de {evaluations.length} evaluaciones
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
                ¿Estás seguro de que quieres eliminar la evaluación de{' '}
                <strong>{evaluationToDelete?.user?.name}</strong>?
              </p>
              <p className="text-muted">
                Esta acción no se puede deshacer y se eliminarán todos los datos asociados a la evaluación.
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
                    Eliminar Evaluación
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

export default EvaluationsPage; 