<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Evaluacion;
use App\Models\Evento;
use App\Models\Employee;

class EvaluacionSeeder extends Seeder
{
    public function run()
    {
        // Obtener algunos eventos y empleados para crear evaluaciones
        $eventos = Evento::take(3)->get();
        $empleados = Employee::take(5)->get();

        if ($eventos->isEmpty() || $empleados->isEmpty()) {
            $this->command->info('No hay eventos o empleados para crear evaluaciones.');
            return;
        }

        foreach ($eventos as $evento) {
            foreach ($empleados as $empleado) {
                // Crear evaluación con estado Pendiente
                Evaluacion::create([
                    'evento_id' => $evento->id,
                    'empleado_id' => $empleado->id,
                    'status' => 'Pendiente',
                    'nota' => null,
                    'respuestas' => null,
                    'preguntas' => [
                        '¿Qué te pareció el evento?',
                        '¿Aplicarías lo aprendido en tu trabajo?',
                        '¿Recomendarías este evento a otros compañeros?',
                        '¿Cómo calificarías la organización del evento?'
                    ],
                ]);
            }
        }

        $this->command->info('Evaluaciones creadas correctamente.');
    }
} 