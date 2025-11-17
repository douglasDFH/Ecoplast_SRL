<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            TipoMaquinariaSeeder::class,
            MaquinariaSeeder::class,
            TurnoSeeder::class,
            FormulacionSeeder::class, 
            RolePermissionSeeder::class,
        ]);
    }
}
