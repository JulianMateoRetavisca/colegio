<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecutar seeders en orden correcto
        $this->call([
            RolesSeeder::class,                      // Primero crear los roles
            UsuariosAdministradoresSeeder::class,    // Luego crear usuarios admin y rector
        ]);
        
        $this->command->info('✓ Base de datos poblada exitosamente.');
    }
}
