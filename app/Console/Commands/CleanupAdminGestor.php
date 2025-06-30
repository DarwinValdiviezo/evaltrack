<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Asistencia;
use App\Models\Evaluacion;
use App\Models\Employee;
use App\Helpers\EmpleadoHelper;

class CleanupAdminGestor extends Command
{
    protected $signature = 'cleanup:admin-gestor';
    protected $description = 'Eliminar asistencias y evaluaciones de admin y gestor (no empleados reales)';

    public function handle()
    {
        $this->info('Limpiando asistencias y evaluaciones de todos los que NO sean empleados reales...');
        
        // Obtener los IDs de usuarios con rol Empleado desde PostgreSQL
        $userIdsEmpleado = EmpleadoHelper::getUserIdsConRolEmpleado();
        // Obtener IDs de empleados que NO son empleados reales
        $no_empleados = Employee::whereNotIn('user_id', $userIdsEmpleado)->get();
        
        foreach ($no_empleados as $persona) {
            $this->info("Limpiando: {$persona->nombre} ({$persona->email})");
            
            // Eliminar asistencias
            $asistencias_eliminadas = Asistencia::where('empleado_id', $persona->id)->delete();
            $this->info("  - Asistencias eliminadas: {$asistencias_eliminadas}");
            
            // Eliminar evaluaciones
            $evaluaciones_eliminadas = Evaluacion::where('empleado_id', $persona->id)->delete();
            $this->info("  - Evaluaciones eliminadas: {$evaluaciones_eliminadas}");
        }
        
        $this->info(PHP_EOL . 'Resumen final:');
        $this->info("Total asistencias: " . Asistencia::count());
        $this->info("Total evaluaciones: " . Evaluacion::count());
        
        // Mostrar empleados restantes con asistencias
        $empleados_con_asistencias = Employee::whereIn('user_id', $userIdsEmpleado)->whereHas('asistencias')->get();
        
        $this->info(PHP_EOL . 'Empleados con asistencias:');
        foreach ($empleados_con_asistencias as $empleado) {
            $asistencias = $empleado->asistencias()->count();
            $evaluaciones = $empleado->evaluaciones()->count();
            $this->info("  - {$empleado->nombre} ({$empleado->email}): {$asistencias} asistencias, {$evaluaciones} evaluaciones");
        }
    }
} 