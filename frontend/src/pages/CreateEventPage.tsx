import React, { useState } from 'react';
import { useMutation, useQueryClient } from '@tanstack/react-query';
import { useNavigate } from 'react-router-dom';
import { eventService } from '../lib/api';
import toast from 'react-hot-toast';
import './EventsPage.css';

const CreateEventPage: React.FC = () => {
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  
  const [formData, setFormData] = useState({
    title: '',
    description: '',
    date: '',
    time: '',
    duration: 60,
    location: '',
    type: '',
    maxAttendees: ''
  });
  
  const [errors, setErrors] = useState<Record<string, string>>({});

  // Mutación para crear evento
  const createMutation = useMutation({
    mutationFn: eventService.create,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['events'] });
      toast.success('Evento creado exitosamente', {
        duration: 4000,
        icon: '✅',
        style: {
          background: '#10b981',
          color: '#fff',
          fontWeight: '600',
        },
      });
      navigate('/events');
    },
    onError: (error: any) => {
      console.error('Error al crear evento:', error);
      const errorMessage = error.response?.data?.message || 'Error al crear evento';
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

  // Tipos de eventos disponibles
  const eventTypes = [
    { value: '', label: 'Seleccionar tipo' },
    { value: 'Capacitación', label: 'Capacitación' },
    { value: 'Taller', label: 'Taller' },
    { value: 'Conferencia', label: 'Conferencia' },
    { value: 'Reunión', label: 'Reunión' },
    { value: 'Otro', label: 'Otro' }
  ];

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
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

    if (!formData.title.trim()) {
      newErrors.title = 'El título es requerido';
    }

    if (!formData.description.trim()) {
      newErrors.description = 'La descripción es requerida';
    }

    if (!formData.date) {
      newErrors.date = 'La fecha es requerida';
    } else {
      const selectedDate = new Date(formData.date);
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      
      if (selectedDate < today) {
        newErrors.date = 'La fecha no puede ser anterior a hoy';
      }
    }

    if (!formData.time) {
      newErrors.time = 'La hora es requerida';
    }

    if (!formData.location.trim()) {
      newErrors.location = 'El lugar es requerido';
    }

    if (!formData.type) {
      newErrors.type = 'El tipo es requerido';
    }

    if (formData.maxAttendees && parseInt(formData.maxAttendees) <= 0) {
      newErrors.maxAttendees = 'El número máximo de asistentes debe ser mayor a 0';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    
    if (validateForm()) {
      // Combinar fecha y hora
      const dateTime = new Date(`${formData.date}T${formData.time}`);
      
      const eventData = {
        title: formData.title,
        description: formData.description,
        date: dateTime.toISOString(),
        duration: parseInt(formData.duration.toString()),
        location: formData.location,
        type: formData.type,
        maxAttendees: formData.maxAttendees ? parseInt(formData.maxAttendees) : null
      };

      createMutation.mutate(eventData);
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

  return (
    <div className="events-page">
      <div className="row justify-content-center">
        <div className="col-lg-8">
          <div className="card shadow-sm border-0">
            <div className="card-header bg-primary text-white">
              <h4 className="mb-0">Crear Nuevo Evento</h4>
            </div>
            <div className="card-body">
              <form onSubmit={handleSubmit}>
                <div className="mb-3">
                  <label htmlFor="title" className="form-label">
                    Nombre del Evento
                  </label>
                  <input
                    type="text"
                    name="title"
                    id="title"
                    className={`form-control ${errors.title ? 'is-invalid' : ''}`}
                    value={formData.title}
                    onChange={handleInputChange}
                    placeholder="Ingrese el nombre del evento"
                  />
                  {errors.title && (
                    <div className="invalid-feedback">{errors.title}</div>
                  )}
                </div>

                <div className="mb-3">
                  <label htmlFor="description" className="form-label">
                    Descripción
                  </label>
                  <textarea
                    name="description"
                    id="description"
                    rows={3}
                    className={`form-control ${errors.description ? 'is-invalid' : ''}`}
                    value={formData.description}
                    onChange={handleInputChange}
                    placeholder="Descripción detallada del evento"
                  />
                  {errors.description && (
                    <div className="invalid-feedback">{errors.description}</div>
                  )}
                </div>

                <div className="row">
                  <div className="col-md-6">
                    <div className="mb-3">
                      <label htmlFor="date" className="form-label">
                        Fecha del Evento
                      </label>
                      <input
                        type="date"
                        name="date"
                        id="date"
                        className={`form-control ${errors.date ? 'is-invalid' : ''}`}
                        value={formData.date}
                        onChange={handleInputChange}
                      />
                      {errors.date && (
                        <div className="invalid-feedback">{errors.date}</div>
                      )}
                    </div>
                  </div>
                  <div className="col-md-6">
                    <div className="mb-3">
                      <label htmlFor="type" className="form-label">
                        Tipo de Evento
                      </label>
                      <select
                        name="type"
                        id="type"
                        className={`form-control ${errors.type ? 'is-invalid' : ''}`}
                        value={formData.type}
                        onChange={handleInputChange}
                      >
                        {eventTypes.map(type => (
                          <option key={type.value} value={type.value}>
                            {type.label}
                          </option>
                        ))}
                      </select>
                      {errors.type && (
                        <div className="invalid-feedback">{errors.type}</div>
                      )}
                    </div>
                  </div>
                </div>

                <div className="row">
                  <div className="col-md-6">
                    <div className="mb-3">
                      <label htmlFor="time" className="form-label">
                        Hora de Inicio
                      </label>
                      <input
                        type="time"
                        name="time"
                        id="time"
                        className={`form-control ${errors.time ? 'is-invalid' : ''}`}
                        value={formData.time}
                        onChange={handleInputChange}
                      />
                      {errors.time && (
                        <div className="invalid-feedback">{errors.time}</div>
                      )}
                    </div>
                  </div>
                  <div className="col-md-6">
                    <div className="mb-3">
                      <label htmlFor="duration" className="form-label">
                        Duración (minutos)
                      </label>
                      <input
                        type="number"
                        name="duration"
                        id="duration"
                        min="15"
                        step="15"
                        className="form-control"
                        value={formData.duration}
                        onChange={handleInputChange}
                      />
                    </div>
                  </div>
                </div>

                <div className="row">
                  <div className="col-md-8">
                    <div className="mb-3">
                      <label htmlFor="location" className="form-label">
                        Lugar
                      </label>
                      <input
                        type="text"
                        name="location"
                        id="location"
                        className={`form-control ${errors.location ? 'is-invalid' : ''}`}
                        value={formData.location}
                        onChange={handleInputChange}
                        placeholder="Ubicación del evento"
                      />
                      {errors.location && (
                        <div className="invalid-feedback">{errors.location}</div>
                      )}
                    </div>
                  </div>
                  <div className="col-md-4">
                    <div className="mb-3">
                      <label htmlFor="maxAttendees" className="form-label">
                        Máx. Asistentes
                      </label>
                      <input
                        type="number"
                        name="maxAttendees"
                        id="maxAttendees"
                        min="1"
                        className={`form-control ${errors.maxAttendees ? 'is-invalid' : ''}`}
                        value={formData.maxAttendees}
                        onChange={handleInputChange}
                        placeholder="Opcional"
                      />
                      {errors.maxAttendees && (
                        <div className="invalid-feedback">{errors.maxAttendees}</div>
                      )}
                    </div>
                  </div>
                </div>

                <div className="d-flex justify-content-between">
                  <a 
                    href="/events" 
                    className="btn btn-secondary rounded-pill px-4"
                    onClick={(e) => {
                      e.preventDefault();
                      navigate('/events');
                    }}
                  >
                    Cancelar
                  </a>
                  <button 
                    type="submit" 
                    className="btn btn-primary rounded-pill px-4"
                    disabled={createMutation.isPending}
                  >
                    {createMutation.isPending ? 'Creando...' : 'Crear Evento'}
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

export default CreateEventPage; 