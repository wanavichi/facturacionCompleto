<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuario admin
        $usuarioAdmin = User::firstOrCreate(
            ['email' => 'admin@factura.com'],
            [
                'name' => 'Admin Principal',
                'password' => Hash::make('password123'),
                'activo' => true,
            ]
        );

        // Obtener el rol admin
        $roleAdmin = Role::where('name', 'Administrador')->first();

        // Asignar rol admin al usuario admin
        if ($roleAdmin) {
            $usuarioAdmin->roles()->syncWithoutDetaching([$roleAdmin->id]);
        }

        // Usuarios con roles específicos
        $usuarios = [
            [
                'name' => 'Usuario Ventas',
                'email' => 'ventas@factura.com',
                'password' => Hash::make('password123'),
                'activo' => true,
                'rol' => 'Ventas',
            ],
            [
                'name' => 'Usuario Bodega',
                'email' => 'bodega@factura.com',
                'password' => Hash::make('password123'),
                'activo' => true,
                'rol' => 'Bodega',
            ],
            [
                'name' => 'Usuario Secretario',
                'email' => 'secretario@factura.com',
                'password' => Hash::make('password123'),
                'activo' => true,
                'rol' => 'Secretario',
            ],
            [
                'name' => 'Validador de Pagos',
                'email' => 'validador@factura.com',
                'password' => Hash::make('password123'),
                'activo' => true,
                'rol' => 'Pagos',
            ],
            [
                'name' => 'Cliente',
                'email' => 'cliente@factura.com',
                'password' => Hash::make('password123'),
                'activo' => true,
                'rol' => 'Cliente'
            ],
        ];

        foreach ($usuarios as $data) {
            // Crear usuario o traer si existe
            $usuario = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => $data['password'],
                    'activo' => $data['activo'],
                ]
            );

            // Obtener el rol por nombre
            $role = Role::where('name', $data['rol'])->first();

            // Asignar rol si existe
            if ($role) {
                $usuario->roles()->syncWithoutDetaching([$role->id]);
            } else {
                info("Rol {$data['rol']} no encontrado para el usuario {$data['email']}");
            }
        }
    }
}
