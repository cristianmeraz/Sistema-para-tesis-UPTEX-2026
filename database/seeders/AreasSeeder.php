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
            ],
            [
                'nombre'      => 'Dirección Académica',
                'descripcion' => 'Gestión de programas educativos, docentes y actividades académicas',
            ],
            [
                'nombre'      => 'Dirección de Administración y Finanzas',
                'descripcion' => 'Administración de recursos humanos, financieros y materiales',
            ],
            [
                'nombre'      => 'Departamento de Servicios Escolares',
                'descripcion' => 'Trámites escolares, registros académicos y atención a estudiantes',
            ],
            [
                'nombre'      => 'Departamento de Planeación e Igualdad de Género',
                'descripcion' => 'Planeación institucional y programas de igualdad de género',
            ],
            [
                'nombre'      => 'Departamento de Recursos Financieros',
                'descripcion' => 'Control presupuestal, contabilidad y recursos financieros',
            ],
        ];

        foreach ($areas as $area) {
            DB::table('areas')->updateOrInsert(
                ['nombre' => $area['nombre']],
                array_merge($area, [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ])
            );
        }
    }
}