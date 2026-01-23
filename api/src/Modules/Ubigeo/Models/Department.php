<?php

namespace Modules\Ubigeo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Department extends Model {

    protected $fillable = [
        'name'
    ];

    public static function list(?string $name)
    {
        return DB::table('departments')
            ->when($name, fn($q) => $q->where('name', 'like', "%$name%"))
            ->select(
                'id as department_id',
                'name'    
            )
            ->get();
    }
}