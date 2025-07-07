import React, { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { useNavigate, useParams } from 'react-router-dom';
import { evaluationService } from '../lib/api';
import toast from 'react-hot-toast';
import './EvaluationsPage.css';

const GradeEvaluationPage: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const { data, isLoading } = useQuery({
    queryKey: ['evaluation', id],
    queryFn: () => evaluationService.getById(Number(id)),
    enabled: !!id,
  });
  const eva = data?.data;

  const [nota, setNota] = useState(eva?.nota ?? '');
  const [observaciones, setObservaciones] = useState(eva?.observaciones ?? '');

  const gradeMutation = useMutation({
    mutationFn: (payload: any) => evaluationService.grade(Number(id), payload),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['evaluation', id] });
      toast.success('Evaluación calificada correctamente', {
        icon: '⭐',
        style: { background: '#10b981', color: '#fff', fontWeight: '600' },
      });
      navigate('/evaluations');
    },
    onError: (error: any) => {
      const errorMessage = error.response?.data?.message || 'Error al calificar';
      toast.error(errorMessage, {
        icon: '❌',
        style: { background: '#ef4444', color: '#fff', fontWeight: '600' },
      });
    },
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!nota || isNaN(Number(nota)) || Number(nota) < 0 || Number(nota) > 10) {
      toast.error('La nota debe ser un número entre 0 y 10');
      return;
    }
    gradeMutation.mutate({ nota: Number(nota), observaciones });
  };

  if (isLoading) {
    return (
      <div className="evaluations-page">
        <div className="text-center">
          <div className="loading-spinner"></div>
          <p>Cargando evaluación...</p>
        </div>
      </div>
    );
  }
  if (!eva) {
    return (
      <div className="evaluations-page">
        <div className="text-center">
          <p>Evaluación no encontrada</p>
          <button className="btn btn-primary" onClick={() => navigate('/evaluations')}>
            Volver a evaluaciones
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="evaluations-page">
      <div className="row justify-content-center">
        <div className="col-lg-7">
          <div className="card shadow-sm border-0">
            <div className="card-header bg-primary text-white">
              <h4 className="mb-0">Calificar Evaluación</h4>
            </div>
            <div className="card-body">
              <div className="mb-3">
                <strong>Empleado:</strong> {eva.user?.name} ({eva.user?.email})
              </div>
              <div className="mb-3">
                <strong>Título:</strong> {eva.titulo}
              </div>
              <div className="mb-3">
                <strong>Preguntas y respuestas:</strong>
                <ul>
                  {eva.event?.questions && eva.event.questions.length > 0 ? (
                    eva.event.questions.map((q: any, idx: number) => (
                      <li key={q.id} className="mb-2">
                        <strong>{q.text}</strong>
                        <div className="text-muted ms-3">
                          Respuesta: {eva.answers?.find((a: any) => a.questionId === q.id)?.response || <span className="text-danger">Sin responder</span>}
                        </div>
                      </li>
                    ))
                  ) : (
                    <li>Sin preguntas</li>
                  )}
                </ul>
              </div>
              <form onSubmit={handleSubmit} className="mt-4">
                <div className="mb-3">
                  <label className="form-label">Nota (0-10)</label>
                  <input
                    type="number"
                    className="form-control"
                    value={nota}
                    min={0}
                    max={10}
                    step={0.1}
                    onChange={e => setNota(e.target.value)}
                    required
                  />
                </div>
                <div className="mb-3">
                  <label className="form-label">Observaciones</label>
                  <textarea
                    className="form-control"
                    value={observaciones}
                    onChange={e => setObservaciones(e.target.value)}
                    rows={3}
                  />
                </div>
                <div className="d-flex justify-content-end gap-2">
                  <button
                    type="button"
                    className="btn btn-secondary"
                    onClick={() => navigate('/evaluations')}
                    disabled={gradeMutation.isPending}
                  >
                    Cancelar
                  </button>
                  <button
                    type="submit"
                    className="btn btn-success"
                    disabled={gradeMutation.isPending}
                  >
                    {gradeMutation.isPending ? 'Guardando...' : 'Guardar calificación'}
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

export default GradeEvaluationPage; 