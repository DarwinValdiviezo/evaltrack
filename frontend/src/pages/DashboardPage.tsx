import React, { useMemo, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useQuery } from '@tanstack/react-query';
import { userService, eventService, attendanceService, evaluationService } from '../lib/api';
import './DashboardPage.css';
import { useAuth } from '../contexts/AuthContext';

const DashboardPage: React.FC = () => {
  const navigate = useNavigate();
  const { canViewUsers, canViewEvents, canViewAttendances, canViewEvaluations, user } = useAuth();
  
  // Consultas a la API
  const { data: users, isLoading: loadingUsers } = useQuery({
    queryKey: ['users'],
    queryFn: userService.getAll,
  });
  const { data: events, isLoading: loadingEvents } = useQuery({
    queryKey: ['events'],
    queryFn: eventService.getAll,
  });
  const { data: attendances, isLoading: loadingAttendances } = useQuery({
    queryKey: ['attendances'],
    queryFn: attendanceService.getAll,
  });
  const { data: evaluations, isLoading: loadingEvaluations } = useQuery({
    queryKey: ['evaluations'],
    queryFn: evaluationService.getAll,
  });

  // Filtros de actividad
  const [activityType, setActivityType] = useState<'ALL'|'USER'|'EVENT'|'ATTENDANCE'|'EVALUATION'>('ALL');
  const [dateRange, setDateRange] = useState<'7D'|'30D'|'ALL'>('7D');

  // Fechas para filtro
  const now = new Date();
  const dateLimit = useMemo(() => {
    if (dateRange === 'ALL') return null;
    const d = new Date();
    d.setDate(d.getDate() - (dateRange === '7D' ? 7 : 30));
    return d;
  }, [dateRange]);

  // Unificar actividad reciente
  const recentActivity = useMemo(() => {
    let acts: any[] = [];
    if (users?.data) acts = acts.concat(users.data.map((u: any) => ({
      type: 'USER',
      date: u.createdAt,
      text: `Usuario creado: ${u.name}`,
      icon: 'bi bi-person-plus',
      link: `/users/${u.id}`
    })));
    if (events?.data) acts = acts.concat(events.data.map((e: any) => ({
      type: 'EVENT',
      date: e.createdAt,
      text: `Evento creado: ${e.title}`,
      icon: 'bi bi-calendar-plus',
      link: `/events`
    })));
    if (attendances?.data) acts = acts.concat(attendances.data.map((a: any) => ({
      type: 'ATTENDANCE',
      date: a.createdAt,
      text: `Asistencia registrada para evento #${a.eventId}`,
      icon: 'bi bi-check2-square',
      link: `/attendances`
    })));
    if (evaluations?.data) acts = acts.concat(evaluations.data.map((ev: any) => ({
      type: 'EVALUATION',
      date: ev.createdAt,
      text: `Evaluación creada para evento #${ev.eventId}`,
      icon: 'bi bi-clipboard-check',
      link: `/evaluations`
    })));
    acts = acts.filter(a => !dateLimit || new Date(a.date) >= dateLimit);
    if (activityType !== 'ALL') acts = acts.filter(a => a.type === activityType);
    acts.sort((a, b) => new Date(b.date).getTime() - new Date(a.date).getTime());
    return acts.slice(0, 8);
  }, [users, events, attendances, evaluations, activityType, dateLimit]);

  // Paneles adicionales
  const activeUsers = users?.data?.filter((u: any) => u.isActive)?.length ?? 0;
  const inactiveUsers = users?.data?.filter((u: any) => !u.isActive)?.length ?? 0;
  const upcomingEvents = events?.data?.filter((e: any) => new Date(e.date) > now)?.slice(0, 3) ?? [];
  const pendingAttendances = attendances?.data?.filter((a: any) => a.status === 'PENDING')?.length ?? 0;
  const confirmedAttendances = attendances?.data?.filter((a: any) => a.status === 'CONFIRMED')?.length ?? 0;
  const pendingEvaluations = evaluations?.data?.filter((e: any) => e.status === 'PENDING')?.length ?? 0;
  const completedEvaluations = evaluations?.data?.filter((e: any) => e.status === 'GRADED')?.length ?? 0;

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('es-ES', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  };

  return (
    <div className="dashboard">
      {/* Header */}
      <div className="dashboard-header">
        <div>
          <h1 className="dashboard-title">Dashboard</h1>
          <p className="dashboard-subtitle">
            Bienvenido, {user?.name}. Aquí tienes un resumen de la información más relevante.
          </p>
        </div>
      </div>

      {/* Filtros */}
      <div className="dashboard-filters">
        <div className="row g-3">
          <div className="col-md-3">
            <select
              className="form-control"
              value={activityType}
              onChange={e => setActivityType(e.target.value as any)}
            >
              <option value="ALL">Todos los tipos</option>
              <option value="USER">Usuarios</option>
              <option value="EVENT">Eventos</option>
              <option value="ATTENDANCE">Asistencias</option>
              <option value="EVALUATION">Evaluaciones</option>
            </select>
          </div>
          <div className="col-md-3">
            <select value={dateRange} onChange={e => setDateRange(e.target.value as any)}>
              <option value="7D">Últimos 7 días</option>
              <option value="30D">Últimos 30 días</option>
              <option value="ALL">Todo</option>
            </select>
          </div>
          <div className="col-md-2">
            <button className="btn btn-primary w-100" onClick={() => { setActivityType('ALL'); setDateRange('7D'); }}>
              Limpiar
            </button>
          </div>
        </div>
      </div>

      {/* Paneles de estadísticas */}
      <div className="stats-grid">
        {canViewUsers() && (
          <div className="stat-card stat-card-primary" onClick={() => navigate('/users')} style={{cursor:'pointer'}}>
            <div className="stat-card-content">
              <div className="stat-card-icon"><i className="bi bi-people"></i></div>
              <div className="stat-card-info">
                <h3 className="stat-card-value">{loadingUsers ? '...' : users?.data?.length ?? 0}</h3>
                <p className="stat-card-title">Usuarios</p>
                <span className="stat-card-description">Activos: {activeUsers} | Inactivos: {inactiveUsers}</span>
              </div>
            </div>
          </div>
        )}
        
        {canViewEvents() && (
          <div className="stat-card stat-card-success" onClick={() => navigate('/events')} style={{cursor:'pointer'}}>
            <div className="stat-card-content">
              <div className="stat-card-icon"><i className="bi bi-calendar-event"></i></div>
              <div className="stat-card-info">
                <h3 className="stat-card-value">{loadingEvents ? '...' : events?.data?.length ?? 0}</h3>
                <p className="stat-card-title">Eventos</p>
                <span className="stat-card-description">Próximos: {upcomingEvents.length}</span>
              </div>
            </div>
          </div>
        )}
        
        {canViewAttendances() && (
          <div className="stat-card stat-card-info" onClick={() => navigate('/attendances')} style={{cursor:'pointer'}}>
            <div className="stat-card-content">
              <div className="stat-card-icon"><i className="bi bi-check2-square"></i></div>
              <div className="stat-card-info">
                <h3 className="stat-card-value">{loadingAttendances ? '...' : attendances?.data?.length ?? 0}</h3>
                <p className="stat-card-title">Asistencias</p>
                <span className="stat-card-description">Pendientes: {pendingAttendances} | Confirmadas: {confirmedAttendances}</span>
              </div>
            </div>
          </div>
        )}
        
        {canViewEvaluations() && (
          <div className="stat-card stat-card-warning" onClick={() => navigate('/evaluations')} style={{cursor:'pointer'}}>
            <div className="stat-card-content">
              <div className="stat-card-icon"><i className="bi bi-clipboard-check"></i></div>
              <div className="stat-card-info">
                <h3 className="stat-card-value">{loadingEvaluations ? '...' : evaluations?.data?.length ?? 0}</h3>
                <p className="stat-card-title">Evaluaciones</p>
                <span className="stat-card-description">Pendientes: {pendingEvaluations} | Completadas: {completedEvaluations}</span>
              </div>
            </div>
          </div>
        )}
      </div>

      {/* Actividad reciente */}
      <div className="recent-activity">
        <div className="activity-header">
          <h2>Actividad Reciente</h2>
        </div>
        <div className="activity-list">
          {recentActivity.length === 0 && (
            <div className="empty-state">
              <i className="bi bi-emoji-frown"></i>
              <p>No hay actividad reciente</p>
            </div>
          )}
          {recentActivity.map((act, idx) => (
            <div key={idx} className="activity-item" style={{cursor:'pointer'}} onClick={() => navigate(act.link)}>
              <div className="activity-icon">
                <i className={act.icon}></i>
              </div>
              <div className="activity-content">
                <p className="activity-text">{act.text}</p>
                <span className="activity-time">{formatDate(act.date)}</span>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};

export default DashboardPage; 