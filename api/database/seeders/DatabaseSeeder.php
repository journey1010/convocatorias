<?php

namespace Database\Seeders;

use Infrastructure\SeederTracker;
use Database\Seeders\{
    Rbac,
    Ubigeo,
    User, 
    Elementos,
    CentrosPoblados
};

class DatabaseSeeder extends SeederTracker
{
    
    public function run(): void
    {
        $this->callIfNotExecuted(Ubigeo::class);
        $this->callIfNotExecuted(Rbac::class);
        //$this->callIfNotExecuted(User::class);
    }
}
