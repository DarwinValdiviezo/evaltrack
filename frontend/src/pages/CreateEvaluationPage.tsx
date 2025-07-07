import React, { useState } from 'react';
import { useMutation, useQueryClient } from '@tanstack/react-query';
import { useNavigate } from 'react-router-dom';
import { evaluationService } from '../lib/api';
import toast from 'react-hot-toast';
import EvaluationForm from './EvaluationForm';
import './EvaluationsPage.css';

const CreateEvaluationPage: React.FC = () => {
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const [errors, setErrors] = useState<Record<string, string>>({});

  const createMutation = useMutation({
    mutationFn: evaluationService.create,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['evaluations'] });
      toast.success('Evaluación creada exitosamente', {
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
      const errorMessage = error.response?.data?.message || 'Error al crear evaluación';
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
    createMutation.mutate(data);
  };

  return (
    <div className="evaluations-page">
      <div className="row justify-content-center">
        <div className="col-lg-8">
          <div className="card shadow-sm border-0">
            <div className="card-header bg-primary text-white">
              <h4 className="mb-0">Crear Nueva Evaluación</h4>
            </div>
            <div className="card-body">
              <EvaluationForm
                onSubmit={handleSubmit}
                isSubmitting={createMutation.isPending}
                errors={errors}
                submitLabel="Crear Evaluación"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default CreateEvaluationPage; 