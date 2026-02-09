<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\ProfessionalRecords\Models\SpecializationArea;

class SpecializationAreasSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            'Ciencias',
            'Ingeniería',
            'Economía',
            'Comercio y Negocios',
            'Derecho',
            'Medicina',
            'Enfermería',
            'Educación',
            'Humanidades',
            'Tecnología de la Información',
            'Administración',
            'Contabilidad',
            'Psicología',
            'Arquitectura',
            'Ciencias Sociales',
            'Comunicación',
            'Turismo y Hotelería',
            'Agronomía',
            'Veterinaria',
            'Biología',
            'Química',
            'Física',
            'Matemáticas',
            'Estadística',
            'Artes',
            'Diseño',
            'Marketing',
            'Recursos Humanos',
            'Logística',
            'Medio Ambiente',
            'Ciencias de la Computación',
        ];

        foreach ($areas as $area) {
            SpecializationArea::firstOrCreate(['name' => $area]);
        }

    }
}
