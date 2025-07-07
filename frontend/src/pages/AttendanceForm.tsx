import React, { useEffect, useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { userService, eventService } from '../lib/api';

interface AttendanceFormProps {
  initialData?: any;
  onSubmit: (data: any) => void;
  isSubmitting: boolean;
  errors?: Record<string, string>;
  submitLabel?: string;
}

const AttendanceForm: React.FC<AttendanceFormProps> = ({ initialData = {}, onSubmit, isSubmitting, errors = {}, submitLabel = 'Guardar' }) => {
  const [formData, setFormData] = useState({
    userId: '',
    eventId: '',
    attendedAt: '',
    status: 'PENDING',
    comment: '',
    ...initialData,
  });

  // Consultas para usuarios y eventos
  const { data: usersData } = useQuery({ queryKey: ['users'], queryFn: userService.getAll });
  const { data: eventsData } = useQuery({ queryKey: ['events'], queryFn: eventService.getAll });
  const users = usersData?.data || [];
  const events = eventsData?.data || [];

  useEffect(() => {
    setFormData((prev: typeof formData) => ({ ...prev, ...initialData }));
  }, [initialData]);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    setFormData((prev: typeof formData) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    onSubmit(formData);
  };

  return (
    <form onSubmit={handleSubmit}>
      <div className="mb-3">
        <label htmlFor="userId" className="form-label">Empleado</label>
        <select
          name="userId"
          id="userId"
          className={`form-control ${errors.userId ? 'is-invalid' : ''}`}
          value={formData.userId}
          onChange={handleChange}
          required
        >
          <option value="">Seleccionar empleado</option>
          {users.map((u: any) => (
            <option key={u.id} value={u.id}>{u.name} ({u.email})</option>
          ))}
        </select>
        {errors.userId && <div className="invalid-feedback">{errors.userId}</div>}
      </div>
      <div className="mb-3">
        <label htmlFor="eventId" className="form-label">Evento</label>
        <select
          name="eventId"
          id="eventId"
          className={`form-control ${errors.eventId ? 'is-invalid' : ''}`}
          value={formData.eventId}
          onChange={handleChange}
          required
        >
          <option value="">Seleccionar evento</option>
          {events.map((e: any) => (
            <option key={e.id} value={e.id}>{e.title} ({new Date(e.date).toLocaleDateString('es-ES')})</option>
          ))}
        </select>
        {errors.eventId && <div className="invalid-feedback">{errors.eventId}</div>}
      </div>
      <div className="row">
        <div className="col-md-6 mb-3">
          <label htmlFor="attendedAt" className="form-label">Fecha y Hora</label>
          <input
            type="datetime-local"
            name="attendedAt"
            id="attendedAt"
            className={`form-control ${errors.attendedAt ? 'is-invalid' : ''}`}
            value={formData.attendedAt}
            onChange={handleChange}
            required
          />
          {errors.attendedAt && <div className="invalid-feedback">{errors.attendedAt}</div>}
        </div>
        <div className="col-md-6 mb-3">
          <label htmlFor="status" className="form-label">Estado</label>
          <select
            name="status"
            id="status"
            className={`form-control ${errors.status ? 'is-invalid' : ''}`}
            value={formData.status}
            onChange={handleChange}
            required
          >
            <option value="PENDING">Pendiente</option>
            <option value="CONFIRMED">Confirmada</option>
            <option value="CANCELLED">Cancelada</option>
          </select>
          {errors.status && <div className="invalid-feedback">{errors.status}</div>}
        </div>
      </div>
      <div className="mb-3">
        <label htmlFor="comment" className="form-label">Comentario</label>
        <textarea
          name="comment"
          id="comment"
          rows={3}
          className={`form-control ${errors.comment ? 'is-invalid' : ''}`}
          value={formData.comment}
          onChange={handleChange}
          placeholder="Comentario sobre la asistencia..."
        />
        {errors.comment && <div className="invalid-feedback">{errors.comment}</div>}
      </div>
      <div className="d-flex justify-content-end gap-2">
        <button type="submit" className="btn btn-success rounded-pill px-4" disabled={isSubmitting}>
          {isSubmitting ? 'Guardando...' : submitLabel}
        </button>
      </div>
    </form>
  );
};

export default AttendanceForm; 