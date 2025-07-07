import React, { useState } from 'react';
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { useNavigate, useParams } from 'react-router-dom';
import { attendanceService } from '../lib/api';
import toast from 'react-hot-toast';
import AttendanceForm from './AttendanceForm';
import './AttendancesPage.css';

const EditAttendancePage: React.FC = () => {
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const { id } = useParams<{ id: string }>();
  const [errors, setErrors] = useState<Record<string, string>>({});

  const { data: attendanceData, isLoading } = useQuery({
    queryKey: ['attendance', id],
    queryFn: () => attendanceService.getById(Number(id)),
    enabled: !!id,
  });

  const updateMutation = useMutation({
    mutationFn: ({ id, data }: { id: number; data: any }) => attendanceService.update(id, data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['attendances'] });
      queryClient.invalidateQueries({ queryKey: ['attendance', id] });
      toast.success('Asistencia actualizada exitosamente', {
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
      const errorMessage = error.response?.data?.message || 'Error al actualizar asistencia';
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
      <div className="attendances-page">
        <div className="text-center">
          <div className="loading-spinner"></div>
          <p>Cargando asistencia...</p>
        </div>
      </div>
    );
  }

  if (!attendanceData?.data) {
    return (
      <div className="attendances-page">
        <div className="text-center">
          <p>Asistencia no encontrada</p>
          <button className="btn btn-primary" onClick={() => navigate('/attendances')}>
            Volver a asistencias
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="attendances-page">
      <div className="row justify-content-center">
        <div className="col-lg-8">
          <div className="card shadow-sm border-0">
            <div className="card-header bg-success text-white">
              <h4 className="mb-0">Editar Asistencia</h4>
            </div>
            <div className="card-body">
              <AttendanceForm
                initialData={attendanceData.data}
                onSubmit={handleSubmit}
                isSubmitting={updateMutation.isPending}
                errors={errors}
                submitLabel="Actualizar Asistencia"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default EditAttendancePage; 