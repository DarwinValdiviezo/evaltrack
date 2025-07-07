<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Evento;
use App\Models\Asistencia;
use App\Models\Evaluacion;
use App\Models\Employee;
use App\Models\User;

class EventoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar el gestor en PostgreSQL
        $gestor = (new User())->setConnection('pgsql')->where('username', 'gestor')->first();
        if (!$gestor) {
            // Si no existe, usar el primer usuario
            $gestor = (new User())->setConnection('pgsql')->first();
        }

        // Crear eventos de ejemplo en MySQL
        $evento1 = (new Evento())->setConnection('mysql')->create([
            'nombre' => 'Capacitación Power BI',
            'descripcion' => 'Aprende a crear dashboards interactivos con Power BI para análisis de datos empresariales.',
            'fecha_evento' => now()->addDays(7),
            'hora_inicio' => '09:00',
            'hora_fin' => '17:00',
            'lugar' => 'Sala de Capacitación A',
            'tipo' => 'Capacitación',
            'estado' => 'Programado',
            'creado_por' => $gestor ? $gestor->id : null,
        ]);

        $evento2 = (new Evento())->setConnection('mysql')->create([
            'nombre' => 'Taller de Comunicación Efectiva',
            'descripcion' => 'Mejora tus habilidades de comunicación interpersonal y presentación en público.',
            'fecha_evento' => now()->addDays(14),
            'hora_inicio' => '14:00',
            'hora_fin' => '18:00',
            'lugar' => 'Auditorio Principal',
            'tipo' => 'Taller',
            'estado' => 'Programado',
            'creado_por' => $gestor ? $gestor->id : null,
        ]);

        $evento3 = (new Evento())->setConnection('mysql')->create([
            'nombre' => 'Reunión de Seguridad Informática',
            'descripcion' => 'Actualización sobre políticas de seguridad y mejores prácticas para proteger información empresarial.',
            'fecha_evento' => now()->addDays(3),
            'hora_inicio' => '10:00',
            'hora_fin' => '12:00',
            'lugar' => 'Sala de Conferencias B',
            'tipo' => 'Reunión',
            'estado' => 'Programado',
            'creado_por' => $gestor ? $gestor->id : null,
        ]);

        // Obtener empleados de MySQL
        $empleados = (new Employee())->setConnection('mysql')->whereNotNull('user_id')->get();

        // Crear asistencias para el evento 1
        foreach ($empleados as $empleado) {
            (new Asistencia())->setConnection('mysql')->create([
                'evento_id' => $evento1->id,
                'empleado_id' => $empleado->id,
                'fecha_asistencia' => $evento1->fecha_evento,
                'hora_asistencia' => '09:00',
                'asistio' => 'Pendiente',
                'status' => 'Registrada',
            ]);
        }

        // Crear asistencias para el evento 2
        foreach ($empleados as $empleado) {
            (new Asistencia())->setConnection('mysql')->create([
                'evento_id' => $evento2->id,
                'empleado_id' => $empleado->id,
                'fecha_asistencia' => $evento2->fecha_evento,
                'hora_asistencia' => '14:00',
                'asistio' => 'Pendiente',
                'status' => 'Registrada',
            ]);
        }

        // Crear evaluaciones para el evento 1 (solo para capacitaciones)
        foreach ($empleados as $empleado) {
            (new Evaluacion())->setConnection('mysql')->create([
                'evento_id' => $evento1->id,
                'empleado_id' => $empleado->id,
                'status' => 'Pendiente',
                'preguntas' => [
                    '¿Qué es Power BI y cuáles son sus principales características?',
                    'Explica la diferencia entre un dataset y un reporte en Power BI.',
                    '¿Cómo crearías una visualización de datos efectiva en Power BI?'
                ],
            ]);
        }
    }
}
