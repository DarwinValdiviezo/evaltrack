<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Evaluacion;
use Illuminate\Support\Facades\DB;

class CleanupEvaluaciones extends Command
{
    protected $signature = 'evaluaciones:cleanup';
    protected $description = 'Limpiar evaluaciones duplicadas';

    public function handle()
    {
        $this->info('Limpiando evaluaciones duplicadas...');
        
        // Encontrar evaluaciones duplicadas
        $duplicates = DB::table('evaluaciones')
            ->select('evento_id', 'empleado_id', DB::raw('COUNT(*) as count'))
            ->groupBy('evento_id', 'empleado_id')
            ->having('count', '>', 1)
            ->get();
        
        foreach ($duplicates as $duplicate) {
            $this->info("Encontradas {$duplicate->count} evaluaciones para evento {$duplicate->evento_id} y empleado {$duplicate->empleado_id}");
            
            // Mantener solo la primera evaluación (la más antigua)
            $evaluaciones = Evaluacion::where('evento_id', $duplicate->evento_id)
                                    ->where('empleado_id', $duplicate->empleado_id)
                                    ->orderBy('created_at')
                                    ->get();
            
            // Eliminar todas excepto la primera
            for ($i = 1; $i < $evaluaciones->count(); $i++) {
                $evaluaciones[$i]->delete();
                $this->info("Eliminada evaluación ID: {$evaluaciones[$i]->id}");
            }
        }
        
        $this->info('Limpieza completada. Total evaluaciones: ' . Evaluacion::count());
    }
} 