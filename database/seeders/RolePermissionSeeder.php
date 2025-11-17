<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Crear permisos básicos con descripciones
        $permissions = [
            // Usuarios
            ['name' => 'ver usuarios', 'description' => 'Puede ver la lista de usuarios del sistema'],
            ['name' => 'crear usuarios', 'description' => 'Puede crear nuevos usuarios'],
            ['name' => 'editar usuarios', 'description' => 'Puede editar información de usuarios existentes'],
            ['name' => 'eliminar usuarios', 'description' => 'Puede eliminar usuarios del sistema'],

            // Roles y permisos
            ['name' => 'ver roles', 'description' => 'Puede ver la lista de roles disponibles'],
            ['name' => 'crear roles', 'description' => 'Puede crear nuevos roles'],
            ['name' => 'editar roles', 'description' => 'Puede editar roles existentes'],
            ['name' => 'eliminar roles', 'description' => 'Puede eliminar roles del sistema'],
            ['name' => 'asignar permisos', 'description' => 'Puede asignar permisos a roles'],

            // Productos
            ['name' => 'ver productos', 'description' => 'Puede ver la lista de productos'],
            ['name' => 'crear productos', 'description' => 'Puede crear nuevos productos'],
            ['name' => 'editar productos', 'description' => 'Puede editar productos existentes'],
            ['name' => 'eliminar productos', 'description' => 'Puede eliminar productos'],

            // Órdenes
            ['name' => 'ver ordenes', 'description' => 'Puede ver las órdenes de producción'],
            ['name' => 'crear ordenes', 'description' => 'Puede crear nuevas órdenes de producción'],
            ['name' => 'editar ordenes', 'description' => 'Puede editar órdenes existentes'],
            ['name' => 'aprobar ordenes', 'description' => 'Puede aprobar órdenes de producción'],

            // Producción
            ['name' => 'ver produccion', 'description' => 'Puede ver registros de producción'],
            ['name' => 'registrar produccion', 'description' => 'Puede registrar nueva producción'],
            ['name' => 'editar produccion', 'description' => 'Puede editar registros de producción'],

            // Inventario
            ['name' => 'ver inventario', 'description' => 'Puede ver el inventario de insumos y productos'],
            ['name' => 'ajustar inventario', 'description' => 'Puede ajustar niveles de inventario'],

            // Calidad
            ['name' => 'ver calidad', 'description' => 'Puede ver inspecciones y registros de calidad'],
            ['name' => 'realizar inspecciones', 'description' => 'Puede realizar inspecciones de calidad'],

            // Mantenimiento
            ['name' => 'ver mantenimiento', 'description' => 'Puede ver registros de mantenimiento'],
            ['name' => 'programar mantenimiento', 'description' => 'Puede programar mantenimientos'],

            // Dashboard
            ['name' => 'ver dashboard', 'description' => 'Puede acceder al dashboard principal'],
            ['name' => 'ver reportes', 'description' => 'Puede ver reportes del sistema'],
        ];

        foreach ($permissions as $permissionData) {
            if (is_array($permissionData)) {
                Permission::firstOrCreate(
                    ['name' => $permissionData['name']],
                    ['description' => $permissionData['description']]
                );
            } else {
                Permission::firstOrCreate(['name' => $permissionData]);
            }
        }

        // Crear roles básicos con descripciones
        $adminRole = Role::firstOrCreate(
            ['name' => 'Administrador'],
            ['description' => 'Acceso completo a todas las funciones del sistema']
        );
        $operadorRole = Role::firstOrCreate(
            ['name' => 'Operador'],
            ['description' => 'Usuario básico para operaciones diarias de producción']
        );
        $supervisorRole = Role::firstOrCreate(
            ['name' => 'Supervisor'],
            ['description' => 'Supervisa operaciones y puede aprobar ciertas acciones']
        );
        $calidadRole = Role::firstOrCreate(
            ['name' => 'Control de Calidad'],
            ['description' => 'Encargado de inspecciones y control de calidad']
        );
        $invitadoRole = Role::firstOrCreate(
            ['name' => 'Invitado'],
            ['description' => 'Acceso limitado de solo lectura']
        );

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
            'aprobar ordenes',
            'ver produccion',
            'registrar produccion',
            'editar produccion',
            'ver inventario',
            'ajustar inventario',
            'ver calidad',
            'ver mantenimiento',
            'programar mantenimiento',
            'ver reportes',
        ]);

        $calidadRole->syncPermissions([
            'ver dashboard',
            'ver calidad',
            'realizar inspecciones',
            'ver productos',
            'ver ordenes',
            'ver produccion',
        ]);

        $invitadoRole->syncPermissions([
            'ver dashboard',
            'ver ordenes',
            'ver produccion',
            'ver productos',
            'ver calidad',
        ]);

        // Asignar rol de administrador al usuario admin si existe
        $adminUser = \App\Models\User::where('email', 'admin@ecoplast.com')->first();
        if ($adminUser) {
            $adminUser->assignRole('Administrador');
        }
    }
}
