import React, { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { useNavigate, useParams } from 'react-router-dom';
import { evaluationService } from '../lib/api';
import toast from 'react-hot-toast';
import './EvaluationsPage.css';

const AnswerEvaluationPage: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const { data, isLoading } = useQuery({
    queryKey: ['evaluation', id],
    queryFn: () => evaluationService.getById(Number(id)),
    enabled: !!id,
  });
  const eva = data?.data;

  const [respuestas, setRespuestas] = useState<string[]>(eva?.preguntas?.map(() => '') || []);

  const answerMutation = useMutation({
    mutationFn: (payload: any) => evaluationService.answer(Number(id), payload),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['evaluation', id] });
      toast.success('Respuestas enviadas correctamente', {
        icon: '✅',
        style: { background: '#10b981', color: '#fff', fontWeight: '600' },
      });
      navigate('/evaluations');
    },
    onError: (error: any) => {
      const errorMessage = error.response?.data?.message || 'Error al enviar respuestas';
      toast.error(errorMessage, {
        icon: '❌',
        style: { background: '#ef4444', color: '#fff', fontWeight: '600' },
      });
    },
  });

  const handleChange = (idx: number, value: string) => {
    setRespuestas(prev => prev.map((r, i) => (i === idx ? value : r)));
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (eva?.preguntas?.length !== respuestas.length || respuestas.some(r => !r.trim())) {
      toast.error('Debes responder todas las preguntas');
      return;
    }
    answerMutation.mutate({ respuestas });
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
            <div className="card-header bg-info text-white">
              <h4 className="mb-0">Responder Evaluación</h4>
            </div>
            <div className="card-body">
              <div className="mb-3">
                <strong>Título:</strong> {eva.titulo}
              </div>
              <form onSubmit={handleSubmit} className="mt-4">
                {eva.preguntas && eva.preguntas.length > 0 ? (
                  eva.preguntas.map((preg: string, idx: number) => (
                    <div className="mb-3" key={idx}>
                      <label className="form-label">
                        {idx + 1}. {preg}
                      </label>
                      <textarea
                        className="form-control"
                        value={respuestas[idx]}
                        onChange={e => handleChange(idx, e.target.value)}
                        rows={2}
                        required
                      />
                    </div>
                  ))
                ) : (
                  <div className="mb-3">No hay preguntas para responder.</div>
                )}
                <div className="d-flex justify-content-end gap-2">
                  <button
                    type="button"
                    className="btn btn-secondary"
                    onClick={() => navigate('/evaluations')}
                    disabled={answerMutation.isPending}
                  >
                    Cancelar
                  </button>
                  <button
                    type="submit"
                    className="btn btn-success"
                    disabled={answerMutation.isPending}
                  >
                    {answerMutation.isPending ? 'Enviando...' : 'Enviar respuestas'}
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

export default AnswerEvaluationPage; 