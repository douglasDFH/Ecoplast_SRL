<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Crear permisos básicos (sin eliminar existentes)
        $permissions = [
            // Usuarios
            'ver usuarios',
            'crear usuarios',
            'editar usuarios',
            'eliminar usuarios',

            // Roles y permisos
            'ver roles',
            'crear roles',
            'editar roles',
            'eliminar roles',
            'asignar permisos',

            // Productos
            'ver productos',
            'crear productos',
            'editar productos',
            'eliminar productos',

            // Órdenes
            'ver ordenes',
            'crear ordenes',
            'editar ordenes',
            'aprobar ordenes',

            // Producción
            'ver produccion',
            'registrar produccion',
            'editar produccion',

            // Inventario
            'ver inventario',
            'ajustar inventario',

            // Calidad
            'ver calidad',
            'realizar inspecciones',

            // Mantenimiento
            'ver mantenimiento',
            'programar mantenimiento',

            // Dashboard
            'ver dashboard',
            'ver reportes',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles básicos solo si no existen
        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
        $operadorRole = Role::firstOrCreate(['name' => 'Operador']);
        $supervisorRole = Role::firstOrCreate(['name' => 'Supervisor']);
        $calidadRole = Role::firstOrCreate(['name' => 'Control de Calidad']);

        // Asignar permisos a roles
        $adminRole->syncPermissions(Permission::all());

        $operadorRole->syncPermissions([
            'ver dashboard',
            'ver ordenes',
            'registrar produccion',
            'ver produccion',
        ]);

        $supervisorRole->syncPermissions([
            'ver dashboard',
            'ver ordenes',
            'crear ordenes',
            'editar ordenes',
            'ver produccion',
            'registrar produccion',
            'ver inventario',
            'ver calidad',
            'ver mantenimiento',
            'ver reportes',
        ]);

        $calidadRole->syncPermissions([
            'ver dashboard',
            'ver calidad',
            'realizar inspecciones',
            'ver productos',
            'ver ordenes',
        ]);

        // Asignar rol de administrador al usuario admin si existe
        $adminUser = \App\Models\User::where('email', 'admin@ecoplast.com')->first();
        if ($adminUser) {
            $adminUser->assignRole('Administrador');
        }
    }
}
