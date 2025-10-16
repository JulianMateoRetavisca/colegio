<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\RolesModel;
use Illuminate\Support\Facades\Hash;

class UsuariosAdministradoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar roles
        $rolAdmin = RolesModel::where('nombre', 'Admin')->first();
        $rolRector = RolesModel::where('nombre', 'Rector')->first();
        
        if (!$rolAdmin || !$rolRector) {
            $this->command->error('Los roles necesarios no existen. Ejecuta primero RolesSeeder.');
            return;
        }

        // Crear usuario administrador
        $admin = User::updateOrCreate(
            ['email' => 'admin@colegio.edu.co'],
            [
                'name' => 'Administrador del Sistema',
                'email' => 'admin@colegio.edu.co',
                'password' => Hash::make('admin123'),
                'roles_id' => $rolAdmin->id,
                'email_verified_at' => now(),
            ]
        );

        // Crear usuario rector
        $rector = User::updateOrCreate(
            ['email' => 'rector@colegio.edu.co'],
            [
                'name' => 'Rector del Colegio',
                'email' => 'rector@colegio.edu.co',
                'password' => Hash::make('rector123'),
                'roles_id' => $rolRector->id,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✓ Usuarios administradores creados exitosamente:');
        $this->command->newLine();
        $this->command->info('ADMINISTRADOR:');
        $this->command->info('  Email: admin@colegio.edu.co');
        $this->command->info('  Contraseña: admin123');
        $this->command->newLine();
        $this->command->info('RECTOR:');
        $this->command->info('  Email: rector@colegio.edu.co');
        $this->command->info('  Contraseña: rector123');
        $this->command->newLine();
        $this->command->warn('⚠ IMPORTANTE: Cambia estas contraseñas después del primer login por seguridad.');
    }
}
