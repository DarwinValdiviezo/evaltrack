import React, { useState } from 'react';
import { useMutation, useQueryClient } from '@tanstack/react-query';
import { useNavigate } from 'react-router-dom';
import { attendanceService } from '../lib/api';
import toast from 'react-hot-toast';
import AttendanceForm from './AttendanceForm';
import './AttendancesPage.css';

const CreateAttendancePage: React.FC = () => {
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const [errors, setErrors] = useState<Record<string, string>>({});

  const createMutation = useMutation({
    mutationFn: attendanceService.create,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['attendances'] });
      toast.success('Asistencia registrada exitosamente', {
        duration: 4000,
        icon: '✅',
        style: {
          background: '#10b981',
          color: '#fff',
          fontWeight: '600',
        },
      });
      navigate('/attendances');
    },
    onError: (error: any) => {
      const errorMessage = error.response?.data?.message || 'Error al registrar asistencia';
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
    <div className="attendances-page">
      <div className="row justify-content-center">
        <div className="col-lg-8">
          <div className="card shadow-sm border-0">
            <div className="card-header bg-success text-white">
              <h4 className="mb-0">Registrar Nueva Asistencia</h4>
            </div>
            <div className="card-body">
              <AttendanceForm
                onSubmit={handleSubmit}
                isSubmitting={createMutation.isPending}
                errors={errors}
                submitLabel="Registrar Asistencia"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default CreateAttendancePage; 