<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Crear permisos
        $permissions = [
            // Gestión de usuarios
            'ver usuarios',
            'crear usuarios',
            'editar usuarios',
            'eliminar usuarios',
            'asignar roles',
            
            // Gestión de empleados
            'ver empleados',
            'crear empleados',
            'editar empleados',
            'eliminar empleados',
            
            // Gestión de eventos
            'ver eventos',
            'crear eventos',
            'editar eventos',
            'eliminar eventos',
            
            // Gestión de asistencias
            'ver asistencias',
            'crear asistencias',
            'editar asistencias',
            'eliminar asistencias',
            'ver mis asistencias',
            
            // Gestión de evaluaciones
            'ver evaluaciones',
            'crear evaluaciones',
            'editar evaluaciones',
            'eliminar evaluaciones',
            'ver mis evaluaciones',
            'responder evaluaciones',
            'calificar evaluaciones',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear roles
        $adminRole = Role::create(['name' => 'Administrador']);
        $gestorRole = Role::create(['name' => 'Gestor de Talento Humano']);
        $empleadoRole = Role::create(['name' => 'Empleado']);

        // Asignar permisos al Administrador (todos los permisos)
        $adminRole->givePermissionTo(Permission::all());

        // Asignar permisos al Gestor de Talento Humano
        $gestorRole->givePermissionTo([
            // Gestión de empleados (ver, crear, editar, pero no eliminar)
            'ver empleados',
            'crear empleados',
            'editar empleados',
            
            // Gestión de eventos (completa)
            'ver eventos',
            'crear eventos',
            'editar eventos',
            'eliminar eventos',
            
            // Gestión de asistencias (completa)
            'ver asistencias',
            'crear asistencias',
            'editar asistencias',
            'eliminar asistencias',
            
            // Gestión de evaluaciones (completa)
            'ver evaluaciones',
            'crear evaluaciones',
            'editar evaluaciones',
            'eliminar evaluaciones',
            'calificar evaluaciones',
        ]);

        // Asignar permisos al Empleado
        $empleadoRole->givePermissionTo([
            'ver mis asistencias',
            'ver mis evaluaciones',
            'responder evaluaciones',
        ]);
    }
}
