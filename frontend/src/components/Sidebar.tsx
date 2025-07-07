import React from 'react';
import { NavLink } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import './Sidebar.css';

const Sidebar: React.FC = () => {
  const { user, logout, canViewUsers, canViewEvents, canViewAttendances, canViewEvaluations } = useAuth();

  const handleLogout = () => {
    logout();
  };

  return (
    <div className="sidebar">
      <div className="sidebar-header">
        <h3>Sistema de Talento</h3>
        <p className="user-info">
          <i className="bi bi-person-circle"></i>
          {user?.name}
          <br />
          <small className="text-muted">{user?.role}</small>
        </p>
      </div>
      
      <nav className="sidebar-nav">
        <ul className="nav-list">
          <li className="nav-item">
            <NavLink to="/" className="nav-link" end>
              <i className="bi bi-speedometer2"></i>
              Dashboard
            </NavLink>
          </li>
          
          {canViewUsers() && (
            <li className="nav-item">
              <NavLink to="/users" className="nav-link">
                <i className="bi bi-people"></i>
                Usuarios
              </NavLink>
            </li>
          )}
          
          {canViewEvents() && (
            <li className="nav-item">
              <NavLink to="/events" className="nav-link">
                <i className="bi bi-calendar-event"></i>
                Eventos
              </NavLink>
            </li>
          )}
          
          {canViewAttendances() && (
            <li className="nav-item">
              <NavLink to="/attendances" className="nav-link">
                <i className="bi bi-check-circle"></i>
                Asistencias
              </NavLink>
            </li>
          )}
          
          {canViewEvaluations() && (
            <li className="nav-item">
              <NavLink to="/evaluations" className="nav-link">
                <i className="bi bi-clipboard-check"></i>
                Evaluaciones
              </NavLink>
            </li>
          )}
        </ul>
      </nav>
      
      <div className="sidebar-footer">
        <button onClick={handleLogout} className="btn-logout">
          <i className="bi bi-box-arrow-right"></i>
          Cerrar Sesión
        </button>
      </div>
    </div>
  );
};

export default Sidebar; 