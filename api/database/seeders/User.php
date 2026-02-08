<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Office\Models\Office;
use Modules\Office\Models\Locale;
use Illuminate\Support\Facades\DB;
use Modules\User\Models\OfficeUser;
use Modules\User\Models\User as Users;

class User extends Seeder
{
    public function run(): void
    {
        DB::transaction(function(){
            $user = Users::create([
                'name' => 'dev',
                'last_name' => 'user', 
                'dni' => '72752219',
                'nickname' => 'devuser',
                'email' => 'ginopaflo001608@gmail.com',
                'phone' => '925849856',
                'password' => 'Hola5.2',
                'status' => 1,
                'level' => 0,
                'created_by' => null,
            ]);
            $user->givePermission('*');

            $locale = Locale::create(['name' => 'sede-central']);

            $office = Office::create([
                'name' => 'linux foundation',
                'locale_id' => $locale->id,
                'status' => 1,
                'level' => 0
            ]);

            OfficeUser::create([
                'office_id' => $office->id,
                'user_id' => $user->id,
            ]);
        });     
    }
}