<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Asistencia;
use Illuminate\Support\Facades\DB;

class CleanupAsistenciasDuplicadas extends Command
{
    protected $signature = 'cleanup:asistencias-duplicadas';
    protected $description = 'Eliminar asistencias duplicadas, dejando solo una por evento y empleado';

    public function handle()
    {
        $this->info('Buscando asistencias duplicadas...');
        $duplicados = DB::table('asistencias')
            ->select('evento_id', 'empleado_id', DB::raw('COUNT(*) as count'))
            ->groupBy('evento_id', 'empleado_id')
            ->having('count', '>', 1)
            ->get();

        foreach ($duplicados as $dup) {
            $asistencias = Asistencia::where('evento_id', $dup->evento_id)
                ->where('empleado_id', $dup->empleado_id)
                ->orderBy('id')
                ->get();
            // Mantener la primera, eliminar el resto
            foreach ($asistencias->slice(1) as $a) {
                $a->delete();
                $this->info("Eliminada asistencia ID: {$a->id} (evento: {$dup->evento_id}, empleado: {$dup->empleado_id})");
            }
        }
        $this->info('Limpieza completada. Total asistencias: ' . Asistencia::count());
    }
} 