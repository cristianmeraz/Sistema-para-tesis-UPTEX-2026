<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AreasSeeder extends Seeder
{
    public function run(): void
    {
        // Departamentos del organigrama institucional UPTEX
        $areas = [
            [
                'nombre'      => 'Rectoría',
                'descripcion' => 'Dirección general y máxima autoridad de la institución',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'nombre'      => 'Dirección Académica',
                'descripcion' => 'Gestión de programas educativos, docentes y actividades académicas',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'nombre'      => 'Dirección de Administración y Finanzas',
                'descripcion' => 'Administración de recursos humanos, financieros y materiales',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'nombre'      => 'Departamento de Servicios Escolares',
                'descripcion' => 'Trámites escolares, registros académicos y atención a estudiantes',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'nombre'      => 'Departamento de Planeación e Igualdad de Género',
                'descripcion' => 'Planeación institucional y programas de igualdad de género',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'nombre'      => 'Departamento de Recursos Financieros',
                'descripcion' => 'Control presupuestal, contabilidad y recursos financieros',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
        ];

        DB::table('areas')->insert($areas);
    }
}