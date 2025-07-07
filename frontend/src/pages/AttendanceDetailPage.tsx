import React from 'react';
import { useQuery } from '@tanstack/react-query';
import { useNavigate, useParams } from 'react-router-dom';
import { attendanceService } from '../lib/api';
import './AttendancesPage.css';

const AttendanceDetailPage: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const { data, isLoading } = useQuery({
    queryKey: ['attendance', id],
    queryFn: () => attendanceService.getById(Number(id)),
    enabled: !!id,
  });
  const att = data?.data;

  const formatDate = (dateString: string) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('es-ES', {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
    });
  };
  const formatTime = (dateString: string) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleTimeString('es-ES', {
      hour: '2-digit',
      minute: '2-digit',
    });
  };

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

  if (isLoading) {
    return (
      <div className="attendances-page">
        <div className="text-center">
          <div className="loading-spinner"></div>
          <p>Cargando asistencia...</p>
        </div>
      </div>
    );
  }

  if (!att) {
    return (
      <div className="attendances-page">
        <div className="text-center">
          <p>Asistencia no encontrada</p>
          <button className="btn btn-primary" onClick={() => navigate('/attendances')}>
            Volver a asistencias
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="attendances-page">
      <div className="row justify-content-center">
        <div className="col-lg-7">
          <div className="card shadow-sm border-0">
            <div className="card-header bg-info text-white">
              <h4 className="mb-0">Detalle de Asistencia</h4>
            </div>
            <div className="card-body">
              <div className="mb-3">
                <strong>Empleado:</strong> {att.user?.name} ({att.user?.email})
              </div>
              <div className="mb-3">
                <strong>Evento:</strong> {att.event?.title} ({att.event?.location})
              </div>
              <div className="mb-3">
                <strong>Fecha:</strong> {formatDate(att.attendedAt)}
              </div>
              <div className="mb-3">
                <strong>Hora:</strong> {formatTime(att.attendedAt)}
              </div>
              <div className="mb-3">
                <strong>Estado:</strong> {getStatusBadge(att.status)}
              </div>
              <div className="mb-3">
                <strong>Comentario:</strong> {att.comment || 'Sin comentarios'}
              </div>
              <div className="d-flex justify-content-end">
                <button className="btn btn-secondary rounded-pill px-4" onClick={() => navigate('/attendances')}>
                  Volver
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default AttendanceDetailPage; 