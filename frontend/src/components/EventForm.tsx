import React from 'react';
import { useForm } from 'react-hook-form';

interface EventFormProps {
  initialData?: any;
  onSubmit: (data: any) => void;
  onClose: () => void;
  loading?: boolean;
}

const EventForm: React.FC<EventFormProps> = ({ initialData, onSubmit, onClose, loading }) => {
  const { register, handleSubmit, formState: { errors } } = useForm({
    defaultValues: initialData || {
      title: '',
      description: '',
      date: '',
      duration: 1,
      location: '',
      maxAttendees: '',
      status: 'ACTIVE',
      isActive: 'true',
    }
  });

  const [preguntas, setPreguntas] = React.useState<string[]>(initialData?.questions?.map((q:any)=>q.text) || ['']);

  const handlePreguntaChange = (idx: number, value: string) => {
    setPreguntas(prev => prev.map((p, i) => (i === idx ? value : p)));
  };
  const addPregunta = () => setPreguntas(prev => [...prev, '']);
  const removePregunta = (idx: number) => setPreguntas(prev => prev.filter((_, i) => i !== idx));

  const handleFormSubmit = (data: any) => {
    data.isActive = data.isActive === 'true';
    data.preguntas = preguntas.filter(p => p.trim() !== '');
    onSubmit(data);
  };

  return (
    <div className="modal show d-block" tabIndex={-1}>
      <div className="modal-dialog">
        <form onSubmit={handleSubmit(handleFormSubmit)}>
          <div className="modal-content">
            <div className="modal-header">
              <h5 className="modal-title">{initialData ? 'Editar evento' : 'Nuevo evento'}</h5>
              <button type="button" className="btn-close" onClick={onClose}></button>
            </div>
            <div className="modal-body">
              <div className="mb-3">
                <label className="form-label">Título</label>
                <input className={`form-control ${errors.title ? 'is-invalid' : ''}`} {...register('title', { required: 'El título es obligatorio' })} />
                {errors.title && <div className="invalid-feedback">{errors.title.message as string}</div>}
              </div>
              <div className="mb-3">
                <label className="form-label">Descripción</label>
                <textarea className="form-control" {...register('description')} />
              </div>
              <div className="mb-3">
                <label className="form-label">Fecha</label>
                <input type="date" className={`form-control ${errors.date ? 'is-invalid' : ''}`} {...register('date', { required: 'La fecha es obligatoria' })} />
                {errors.date && <div className="invalid-feedback">{errors.date.message as string}</div>}
              </div>
              <div className="mb-3">
                <label className="form-label">Duración (horas)</label>
                <input type="number" min={1} className="form-control" {...register('duration', { required: true })} />
              </div>
              <div className="mb-3">
                <label className="form-label">Lugar</label>
                <input className="form-control" {...register('location')} />
              </div>
              <div className="mb-3">
                <label className="form-label">Máximo de asistentes</label>
                <input type="number" min={1} className="form-control" {...register('maxAttendees')} />
              </div>
              <div className="mb-3">
                <label className="form-label">Estado</label>
                <select className="form-select" {...register('status', { required: true })}>
                  <option value="ACTIVE">Activo</option>
                  <option value="INACTIVE">Inactivo</option>
                </select>
              </div>
              <div className="mb-3">
                <label className="form-label">¿Evento activo?</label>
                <select className="form-select" {...register('isActive', { required: true })}>
                  <option value="true">Sí</option>
                  <option value="false">No</option>
                </select>
              </div>
              <div className="mb-3">
                <label className="form-label">Preguntas de evaluación</label>
                {preguntas.map((preg, idx) => (
                  <div className="d-flex mb-2" key={idx}>
                    <input
                      className="form-control me-2"
                      value={preg}
                      onChange={e => handlePreguntaChange(idx, e.target.value)}
                      placeholder={`Pregunta ${idx + 1}`}
                      required
                    />
                    <button type="button" className="btn btn-danger" onClick={() => removePregunta(idx)} title="Eliminar pregunta">
                      <i className="bi bi-trash"></i>
                    </button>
                  </div>
                ))}
                <button type="button" className="btn btn-primary" onClick={addPregunta}>
                  <i className="bi bi-plus"></i> Agregar pregunta
                </button>
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

export default EventForm; 