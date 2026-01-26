<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Ubigeo extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = json_decode(file_get_contents(storage_path('ubigeo/ubigeo_peru_2016_departamentos.json')), true);
        DB::unprepared('ALTER TABLE departments  AUTO_INCREMENT = 0');
        foreach ($departments as $department) {
            DB::table('departments')->insert([
                'id' => $department['id'],
                'name' => $department['name'],
            ]);
        }

        DB::unprepared('ALTER TABLE departments  AUTO_INCREMENT = 1');


        $provinces = json_decode(file_get_contents(storage_path('ubigeo/ubigeo_peru_2016_provincias.json')), true);
        foreach ($provinces as $province) {
            $id = (int) $province['department_id'];
            $departmentId = DB::table('departments')->where('id', $id )->first();
            DB::table('provinces')->insert([
                'name' => $province['name'],
                'department_id' => $departmentId->id
            ]);
        }

        $districts = json_decode(file_get_contents(storage_path('ubigeo/ubigeo_peru_2016_distritos.json')), true);
        foreach ($districts as $district) {

            DB::table('districts')->insert([
                'name' => $district['name'],
                'department_id' => $district['department_id'],
                'province_id' =>  $district['province_id']
            ]);
        }
    }
}