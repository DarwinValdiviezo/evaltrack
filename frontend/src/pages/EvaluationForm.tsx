import React, { useEffect, useState, useMemo } from 'react';
import { useQuery } from '@tanstack/react-query';
import { userService } from '../lib/api';
import './EvaluationsPage.css';

interface EvaluationFormProps {
  initialData?: any;
  onSubmit: (data: any) => void;
  isSubmitting: boolean;
  errors?: Record<string, string>;
  submitLabel?: string;
}

const statusOptions = [
  { value: '', label: 'Seleccionar estado' },
  { value: 'Pendiente', label: 'Pendiente' },
  { value: 'Disponible', label: 'Disponible' },
  { value: 'Completada', label: 'Completada' },
  { value: 'Calificada', label: 'Calificada' },
];

const EvaluationForm: React.FC<EvaluationFormProps> = ({ initialData = {}, onSubmit, isSubmitting, errors = {}, submitLabel = 'Guardar' }) => {
  // Inicializar el estado una sola vez
  const initialFormData = useMemo(() => ({
    empleadoId: '',
    fechaEvaluacion: '',
    titulo: '',
    descripcion: '',
    status: '',
    nota: '',
    preguntas: [''],
    ...initialData,
  }), []);

  const [formData, setFormData] = useState(initialFormData);

  // Consultar empleados
  const { data: usersData } = useQuery({ queryKey: ['users'], queryFn: userService.getAll });
  const empleados = usersData?.data || [];

  // Solo actualizar si initialData cambia significativamente
  useEffect(() => {
    if (initialData && Object.keys(initialData).length > 0) {
      setFormData((prev: any) => ({
        ...prev,
        ...initialData,
        preguntas: initialData.preguntas || [''],
      }));
    }
  }, [initialData?.id, initialData?.titulo]); // Solo dependencias específicas

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    setFormData((prev: any) => ({ ...prev, [name]: value }));
  };

  // Preguntas dinámicas
  const handlePreguntaChange = (idx: number, value: string) => {
    setFormData((prev: any) => ({
      ...prev,
      preguntas: prev.preguntas.map((p: string, i: number) => (i === idx ? value : p)),
    }));
  };
  const addPregunta = () => {
    setFormData((prev: any) => ({ ...prev, preguntas: [...prev.preguntas, ''] }));
  };
  const removePregunta = (idx: number) => {
    setFormData((prev: any) => ({ ...prev, preguntas: prev.preguntas.filter((_: string, i: number) => i !== idx) }));
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    onSubmit(formData);
  };

  return (
    <form onSubmit={handleSubmit}>
      <div className="row">
        <div className="col-md-6 mb-3">
          <label htmlFor="empleadoId" className="form-label">Empleado</label>
          <select
            name="empleadoId"
            id="empleadoId"
            className={`form-control ${errors.empleadoId ? 'is-invalid' : ''}`}
            value={formData.empleadoId}
            onChange={handleChange}
            required
          >
            <option value="">Seleccionar empleado</option>
            {empleados.map((u: any) => (
              <option key={u.id} value={u.id}>{u.name} ({u.email})</option>
            ))}
          </select>
          {errors.empleadoId && <div className="invalid-feedback">{errors.empleadoId}</div>}
        </div>
        <div className="col-md-6 mb-3">
          <label htmlFor="fechaEvaluacion" className="form-label">Fecha de Evaluación</label>
          <input
            type="date"
            name="fechaEvaluacion"
            id="fechaEvaluacion"
            className={`form-control ${errors.fechaEvaluacion ? 'is-invalid' : ''}`}
            value={formData.fechaEvaluacion}
            onChange={handleChange}
            required
          />
          {errors.fechaEvaluacion && <div className="invalid-feedback">{errors.fechaEvaluacion}</div>}
        </div>
      </div>
      <div className="mb-3">
        <label htmlFor="titulo" className="form-label">Título de la Evaluación</label>
        <input
          type="text"
          name="titulo"
          id="titulo"
          className={`form-control ${errors.titulo ? 'is-invalid' : ''}`}
          value={formData.titulo}
          onChange={handleChange}
          placeholder="Ej: Evaluación de Desempeño Q1 2024"
          required
        />
        {errors.titulo && <div className="invalid-feedback">{errors.titulo}</div>}
      </div>
      <div className="mb-3">
        <label htmlFor="descripcion" className="form-label">Descripción</label>
        <textarea
          name="descripcion"
          id="descripcion"
          rows={3}
          className={`form-control ${errors.descripcion ? 'is-invalid' : ''}`}
          value={formData.descripcion}
          onChange={handleChange}
          placeholder="Describe el propósito y objetivos de esta evaluación..."
        />
        {errors.descripcion && <div className="invalid-feedback">{errors.descripcion}</div>}
      </div>
      <div className="row">
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
            {statusOptions.map(opt => (
              <option key={opt.value} value={opt.value}>{opt.label}</option>
            ))}
          </select>
          {errors.status && <div className="invalid-feedback">{errors.status}</div>}
        </div>
        <div className="col-md-6 mb-3">
          <label htmlFor="nota" className="form-label">Nota (opcional)</label>
          <input
            type="number"
            name="nota"
            id="nota"
            step="0.1"
            min="0"
            max="10"
            className={`form-control ${errors.nota ? 'is-invalid' : ''}`}
            value={formData.nota}
            onChange={handleChange}
            placeholder="0-10"
          />
          {errors.nota && <div className="invalid-feedback">{errors.nota}</div>}
        </div>
      </div>
      <div className="mb-3">
        <label className="form-label">Preguntas de la Evaluación</label>
        {formData.preguntas.map((pregunta: string, idx: number) => (
          <div className="pregunta-item" key={idx}>
            <input
              type="text"
              className="form-control"
              value={pregunta}
              onChange={e => handlePreguntaChange(idx, e.target.value)}
              placeholder={`Escribe la pregunta ${idx + 1} aquí...`}
              required
            />
            <button type="button" className="btn-remove-pregunta" onClick={() => removePregunta(idx)} title="Eliminar pregunta">
              <i className="bi bi-trash"></i>
            </button>
          </div>
        ))}
        <button type="button" className="btn-add-pregunta" onClick={addPregunta}>
          <i className="bi bi-plus"></i> Agregar pregunta
        </button>
        {errors.preguntas && <div className="invalid-feedback d-block">{errors.preguntas}</div>}
      </div>
      <div className="d-flex justify-content-end gap-2">
        <button type="submit" className="btn btn-success rounded-pill px-4" disabled={isSubmitting}>
          {isSubmitting ? 'Guardando...' : submitLabel}
        </button>
      </div>
    </form>
  );
};

export default EvaluationForm; 