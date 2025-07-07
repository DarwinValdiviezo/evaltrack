import React, { useState } from 'react';
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { useNavigate, useParams } from 'react-router-dom';
import { evaluationService } from '../lib/api';
import toast from 'react-hot-toast';
import EvaluationForm from './EvaluationForm';
import './EvaluationsPage.css';

const EditEvaluationPage: React.FC = () => {
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const { id } = useParams<{ id: string }>();
  const [errors, setErrors] = useState<Record<string, string>>({});

  const { data: evaluationData, isLoading } = useQuery({
    queryKey: ['evaluation', id],
    queryFn: () => evaluationService.getById(Number(id)),
    enabled: !!id,
  });

  const updateMutation = useMutation({
    mutationFn: ({ id, data }: { id: number; data: any }) => evaluationService.update(id, data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['evaluations'] });
      queryClient.invalidateQueries({ queryKey: ['evaluation', id] });
      toast.success('Evaluación actualizada exitosamente', {
        duration: 4000,
        icon: '✅',
        style: {
          background: '#10b981',
          color: '#fff',
          fontWeight: '600',
        },
      });
      navigate('/evaluations');
    },
    onError: (error: any) => {
      const errorMessage = error.response?.data?.message || 'Error al actualizar evaluación';
      toast.error(errorMessage, {
        duration: 5000,
        icon: '❌',
        style: {
          background: '#ef4444',
          color: '#fff',
          fontWeight: '600',
        },
      });
      if (error.response?.data?.errors) {
        setErrors(error.response.data.errors);
      }
    },
  });

  const handleSubmit = (data: any) => {
    setErrors({});
    if (id) {
      updateMutation.mutate({ id: Number(id), data });
    }
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

  if (!evaluationData?.data) {
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
        <div className="col-lg-8">
          <div className="card shadow-sm border-0">
            <div className="card-header bg-primary text-white">
              <h4 className="mb-0">Editar Evaluación</h4>
            </div>
            <div className="card-body">
              <EvaluationForm
                initialData={evaluationData.data}
                onSubmit={handleSubmit}
                isSubmitting={updateMutation.isPending}
                errors={errors}
                submitLabel="Actualizar Evaluación"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default EditEvaluationPage; 