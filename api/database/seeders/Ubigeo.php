<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Ubigeo extends Seeder
{
    public function run(): void
    {
        // 1. Limpiar tablas y reiniciar secuencias (Truncate con Restart Identity)
        // Esto es equivalente al AUTO_INCREMENT = 0 pero compatible con Postgres
        DB::statement('TRUNCATE TABLE districts, provinces, departments RESTART IDENTITY CASCADE');

        // --- DEPARTAMENTOS ---
        $departmentsData = json_decode(file_get_contents(storage_path('ubigeo/ubigeo_peru_2016_departamentos.json')), true);
        $departments = array_map(fn($d) => [
            'id' => (int)$d['id'],
            'name' => $d['name'],
            'created_at' => now(),
            'updated_at' => now()
        ], $departmentsData);
        
        DB::table('departments')->insert($departments);
        
        // Sincronizar la secuencia de Postgres para que empiece después del ID más alto insertado
        $this->syncSequence('departments');

        // --- PROVINCIAS ---
        $provincesData = json_decode(file_get_contents(storage_path('ubigeo/ubigeo_peru_2016_provincias.json')), true);
        $provinces = array_map(fn($p) => [
            'name' => $p['name'],
            'department_id' => (int)$p['department_id'],
            'created_at' => now(),
            'updated_at' => now()
        ], $provincesData);

        DB::table('provinces')->insert($provinces);
        $this->syncSequence('provinces');

        // --- DISTRITOS ---
        $districtsData = json_decode(file_get_contents(storage_path('ubigeo/ubigeo_peru_2016_distritos.json')), true);
        // Insertamos por lotes (chunks) para evitar saturar la memoria si el JSON es muy grande
        foreach (array_chunk($districtsData, 100) as $chunk) {
            $data = array_map(fn($d) => [
                'name' => $d['name'],
                'department_id' => (int)$d['department_id'],
                'province_id' => (int)$d['province_id'],
                'created_at' => now(),
                'updated_at' => now()
            ], $chunk);
            DB::table('districts')->insert($data);
        }
        $this->syncSequence('districts');
    }

    /**
     * Sincroniza la secuencia de IDs en PostgreSQL
     */
    private function syncSequence(string $table): void
    {
        // Solo para PostgreSQL
        if (config('database.default') === 'pgsql') {
            DB::statement("SELECT setval('{$table}_id_seq', (SELECT MAX(id) FROM {$table}))");
        }
    }
}