<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        // Crear roles
        $usuarioNormal = Role::firstOrCreate(['name' => 'Usuario Normal']);
        $tecnicoAnalista = Role::firstOrCreate(['name' => 'Técnico Analista']);

        // Crear usuario normal
        $user1 = User::firstOrCreate(
            ['email' => 'usuario@demo.com'],
            [
                'name' => 'Usuario Demo',
                'password' => Hash::make('password'),
            ]
        );
        $user1->assignRole($usuarioNormal);

        // Crear técnico analista
        $user2 = User::firstOrCreate(
            ['email' => 'tecnico@demo.com'],
            [
                'name' => 'Técnico Demo',
                'password' => Hash::make('password'),
            ]
        );
        $user2->assignRole($tecnicoAnalista);
    }
}
