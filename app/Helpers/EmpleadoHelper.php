<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class EmpleadoHelper
{
    public static function getUserIdsConRolEmpleado()
    {
        // Consulta directa a PostgreSQL para obtener los IDs de usuarios con rol 'Empleado'
        $ids = DB::connection('pgsql')
            ->table('users')
            ->join('model_has_roles', function($join) {
                $join->on('users.id', '=', 'model_has_roles.model_id')
                     ->where('model_has_roles.model_type', 'App\\Models\\User');
            })
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'Empleado')
            ->pluck('users.id')
            ->toArray();
        return $ids;
    }
} 