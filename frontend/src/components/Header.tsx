import React, { useState } from 'react';
import { useAuth } from '../contexts/AuthContext';
import './Header.css';
import { useNavigate } from 'react-router-dom';

const Header: React.FC = () => {
  const { user, logout } = useAuth();
  const [showMenu, setShowMenu] = useState(false);
  const navigate = useNavigate();

  return (
    <header className="header">
      <div className="header-content">
        <div className="header-left">
          <h1 className="header-title">Panel de Control</h1>
          <p className="header-subtitle">Sistema de Gestión de Talento Humano</p>
        </div>
        
        <div className="header-right">
          <div className="header-actions">
            <button className="notification-btn" title="Notificaciones">
              <i className="bi bi-bell"></i>
              <span className="notification-badge">3</span>
            </button>
            
            <div className="user-dropdown">
              <button 
                className="user-dropdown-btn" 
                onClick={() => setShowMenu(!showMenu)}
              >
                <div className="user-avatar">
                  {user?.name?.[0]?.toUpperCase() || 'U'}
                </div>
                <div className="user-info">
                  <span className="user-name">{user?.name}</span>
                  <span className="user-role">{user?.role?.replace('_', ' ').toLowerCase()}</span>
                </div>
                <i className="bi bi-chevron-down"></i>
              </button>
              
              {showMenu && (
                <div className="dropdown-menu">
                  <div className="dropdown-header">
                    <div className="dropdown-user-info">
                      <div className="dropdown-avatar">
                        {user?.name?.[0]?.toUpperCase() || 'U'}
                      </div>
                      <div>
                        <div className="dropdown-name">{user?.name}</div>
                        <div className="dropdown-email">{user?.email}</div>
                      </div>
                    </div>
                  </div>
                  <div className="dropdown-divider"></div>
                  <button className="dropdown-item" onClick={() => { setShowMenu(false); navigate('/perfil'); }}>
                    <i className="bi bi-person"></i>
                    <span>Mi Perfil</span>
                  </button>
                  <button className="dropdown-item">
                    <i className="bi bi-gear"></i>
                    <span>Configuración</span>
                  </button>
                  <div className="dropdown-divider"></div>
                  <button className="dropdown-item text-danger" onClick={() => { setShowMenu(false); logout(); navigate('/login'); }}>
                    <i className="bi bi-box-arrow-right"></i>
                    <span>Cerrar Sesión</span>
                  </button>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </header>
  );
};

export default Header; 