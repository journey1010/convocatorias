<?php

namespace Infrastructure;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

abstract class SeederTracker extends Seeder
{
    abstract public function run(): void;

    public function callIfNotExecuted(string $class): void
    {
        $alreadyExecuted  = DB::table('migration_seeder')->where('seeder_name', $class)->exists();
        if(!$alreadyExecuted){
            $this->call($class);
            DB::table('migration_seeder')->insert([
                'seeder_name' => $class,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
