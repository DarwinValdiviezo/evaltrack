import React from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { useNavigate, useParams } from 'react-router-dom';
import { evaluationService } from '../lib/api';
import './EvaluationsPage.css';
import { useAuth } from '../contexts/AuthContext';

const EvaluationDetailPage: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const { data, isLoading } = useQuery({
    queryKey: ['evaluation', id],
    queryFn: () => evaluationService.getById(Number(id)),
    enabled: !!id,
  });
  const eva = data?.data;
  const { user } = useAuth();
  const queryClient = useQueryClient();
  const [answers, setAnswers] = React.useState<{ [key: number]: string }>({});
  const [isEditing, setIsEditing] = React.useState(false);

  React.useEffect(() => {
    if (eva?.answers) {
      const initial: { [key: number]: string } = {};
      eva.answers.forEach((a: any) => { initial[a.questionId] = a.response; });
      setAnswers(initial);
    }
  }, [eva?.id]);

  const answerMutation = useMutation({
    mutationFn: (payload: any) => evaluationService.answer(eva.id, payload),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['evaluation', eva.id] });
      setIsEditing(false);
    },
  });

  const handleAnswerChange = (qid: number, value: string) => {
    setAnswers((prev) => ({ ...prev, [qid]: value }));
  };

  const handleSubmitAnswers = (e: React.FormEvent) => {
    e.preventDefault();
    const payload = { answers: Object.entries(answers).map(([questionId, response]) => ({ questionId: Number(questionId), response })) };
    answerMutation.mutate(payload);
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
              <h4 className="mb-0">Detalle de Evaluación</h4>
            </div>
            <div className="card-body">
              <div className="mb-3">
                <strong>Empleado:</strong> {eva.user?.name} ({eva.user?.email})
              </div>
              <div className="mb-3">
                <strong>Título:</strong> {eva.titulo}
              </div>
              <div className="mb-3">
                <strong>Fecha:</strong> {eva.fechaEvaluacion ? new Date(eva.fechaEvaluacion).toLocaleDateString('es-ES') : '-'}
              </div>
              <div className="mb-3">
                <strong>Estado:</strong> {eva.status}
              </div>
              <div className="mb-3">
                <strong>Nota:</strong> {eva.nota ?? 'Sin calificar'}
              </div>
              <div className="mb-3">
                <strong>Descripción:</strong> {eva.descripcion || 'Sin descripción'}
              </div>
              <div className="mb-3">
                <strong>Evento:</strong> {eva.event?.title || '-'}<br/>
                <strong>Tipo:</strong> {eva.event?.type || '-'}<br/>
                <strong>Fecha evento:</strong> {eva.event?.date ? new Date(eva.event.date).toLocaleDateString('es-ES') : '-'}
              </div>
              <div className="mb-3">
                <strong>Preguntas y respuestas:</strong>
                {eva.event?.questions && eva.event.questions.length > 0 ? (
                  <div className="table-responsive">
                    <table className="table table-bordered table-sm align-middle">
                      <thead className="table-light">
                        <tr>
                          <th style={{width:'60%'}}>Pregunta</th>
                          <th>Respuesta</th>
                        </tr>
                      </thead>
                      <tbody>
                        {eva.event.questions.map((q: any) => (
                          <tr key={q.id}>
                            <td><b>{q.text}</b></td>
                            <td>
                              <div className="bg-light p-2 rounded" style={{minHeight:40}}>
                                {eva.answers?.find((a: any) => a.questionId === q.id)?.response
                                  ? <span style={{color:'#222'}}>{eva.answers.find((a: any) => a.questionId === q.id).response}</span>
                                  : <i style={{color:'#888'}}>Sin respuesta</i>}
                              </div>
                            </td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>
                ) : (
                  <div className="alert alert-warning">Sin preguntas</div>
                )}
              </div>
              {eva.respuestas && eva.respuestas.length > 0 && (
                <div className="mb-3">
                  <strong>Respuestas:</strong>
                  <ul>
                    {eva.respuestas.map((resp: string, idx: number) => (
                      <li key={idx}>{resp}</li>
                    ))}
                  </ul>
                </div>
              )}
              <div className="d-flex justify-content-end">
                <button className="btn btn-secondary rounded-pill px-4" onClick={() => navigate('/evaluations')}>
                  Volver
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default EvaluationDetailPage; 