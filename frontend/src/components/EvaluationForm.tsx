import React from 'react';
import { useForm } from 'react-hook-form';

interface EvaluationFormProps {
  initialData?: any;
  onSubmit: (data: any) => void;
  onClose: () => void;
  loading?: boolean;
  events: any[];
  users: any[];
}

const EvaluationForm: React.FC<EvaluationFormProps> = ({ initialData, onSubmit, onClose, loading, events, users }) => {
  const { register, handleSubmit, formState: { errors } } = useForm({
    defaultValues: initialData || {
      eventId: '',
      userId: '',
      status: 'PENDING',
      score: '',
      feedback: '',
    }
  });

  return (
    <div className="modal show d-block" tabIndex={-1}>
      <div className="modal-dialog">
        <form onSubmit={handleSubmit(onSubmit)}>
          <div className="modal-content">
            <div className="modal-header">
              <h5 className="modal-title">{initialData ? 'Editar evaluación' : 'Nueva evaluación'}</h5>
              <button type="button" className="btn-close" onClick={onClose}></button>
            </div>
            <div className="modal-body">
              <div className="mb-3">
                <label className="form-label">Evento</label>
                <select className="form-select" {...register('eventId', { required: true })}>
                  <option value="">Selecciona un evento</option>
                  {events.map(ev => <option key={ev.id} value={ev.id}>{ev.title}</option>)}
                </select>
                {errors.eventId && <div className="invalid-feedback d-block">El evento es obligatorio</div>}
              </div>
              <div className="mb-3">
                <label className="form-label">Usuario</label>
                <select className="form-select" {...register('userId', { required: true })}>
                  <option value="">Selecciona un usuario</option>
                  {users.map(u => <option key={u.id} value={u.id}>{u.name}</option>)}
                </select>
                {errors.userId && <div className="invalid-feedback d-block">El usuario es obligatorio</div>}
              </div>
              <div className="mb-3">
                <label className="form-label">Estado</label>
                <select className="form-select" {...register('status', { required: true })}>
                  <option value="PENDING">Pendiente</option>
                  <option value="SUBMITTED">Enviada</option>
                  <option value="GRADED">Calificada</option>
                </select>
              </div>
              <div className="mb-3">
                <label className="form-label">Calificación</label>
                <input type="number" min={0} max={100} className="form-control" {...register('score')} />
              </div>
              <div className="mb-3">
                <label className="form-label">Feedback</label>
                <textarea className="form-control" {...register('feedback')} />
              </div>
            </div>
            <div className="modal-footer">
              <button type="button" className="btn btn-secondary" onClick={onClose}>Cancelar</button>
              <button type="submit" className="btn btn-success" disabled={loading}>
                {loading ? 'Guardando...' : 'Guardar'}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  );
};

export default EvaluationForm; 