<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UsuariosSeeder extends Seeder
{
    public function run(): void
    {
        // IDs de áreas según AreasSeeder (orden de inserción):
        // 1=Rectoría, 2=Dirección Académica, 3=Dir. Admin y Finanzas,
        // 4=Depto. Servicios Escolares, 5=Depto. Planeación, 6=Depto. Recursos Financieros

        $usuarios = [
            // ADMINISTRADOR
            [
                'nombre'            => 'Carlos',
                'apellido'          => 'Administrador',
                'correo'            => 'admin@uptex.edu.mx',
                'password'          => Hash::make('admin123'),
                'id_rol'            => 1,
                'area_id'           => null,
                'activo'            => true,
                'email_verified_at' => Carbon::now(),
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],

            // TÉCNICOS (sin área institucional asignada)
            [
                'nombre'            => 'María',
                'apellido'          => 'Técnico Soporte',
                'correo'            => 'maria.tecnico@uptex.edu.mx',
                'password'          => Hash::make('tecnico123'),
                'id_rol'            => 2,
                'area_id'           => null,
                'activo'            => true,
                'email_verified_at' => Carbon::now(),
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'nombre'            => 'Juan',
                'apellido'          => 'Técnico Redes',
                'correo'            => 'juan.tecnico@uptex.edu.mx',
                'password'          => Hash::make('tecnico123'),
                'id_rol'            => 2,
                'area_id'           => null,
                'activo'            => true,
                'email_verified_at' => Carbon::now(),
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'nombre'            => 'Pedro',
                'apellido'          => 'Técnico Sistemas',
                'correo'            => 'pedro.tecnico@uptex.edu.mx',
                'password'          => Hash::make('tecnico123'),
                'id_rol'            => 2,
                'area_id'           => null,
                'activo'            => true,
                'email_verified_at' => Carbon::now(),
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],

            // USUARIOS NORMALES (con área institucional del organigrama)
            [
                'nombre'            => 'Ana',
                'apellido'          => 'García López',
                'correo'            => 'ana.garcia@uptex.edu.mx',
                'password'          => Hash::make('usuario123'),
                'id_rol'            => 3,
                'area_id'           => 2, // Dirección Académica
                'activo'            => true,
                'email_verified_at' => Carbon::now(),
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'nombre'            => 'Luis',
                'apellido'          => 'Martínez Pérez',
                'correo'            => 'luis.martinez@uptex.edu.mx',
                'password'          => Hash::make('usuario123'),
                'id_rol'            => 3,
                'area_id'           => 3, // Dirección de Administración y Finanzas
                'activo'            => true,
                'email_verified_at' => Carbon::now(),
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'nombre'            => 'Carmen',
                'apellido'          => 'Rodríguez Silva',
                'correo'            => 'carmen.rodriguez@uptex.edu.mx',
                'password'          => Hash::make('usuario123'),
                'id_rol'            => 3,
                'area_id'           => 4, // Departamento de Servicios Escolares
                'activo'            => true,
                'email_verified_at' => Carbon::now(),
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'nombre'            => 'Roberto',
                'apellido'          => 'Hernández Cruz',
                'correo'            => 'roberto.hernandez@uptex.edu.mx',
                'password'          => Hash::make('usuario123'),
                'id_rol'            => 3,
                'area_id'           => 6, // Departamento de Recursos Financieros
                'activo'            => true,
                'email_verified_at' => Carbon::now(),
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
        ];

        DB::table('usuarios')->insert($usuarios);
    }
}