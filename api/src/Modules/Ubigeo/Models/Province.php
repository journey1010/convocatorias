<?php

namespace Modules\Ubigeo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Province extends Model {

    protected $fillable = [
        'name',
        'department_id'
    ];

    public static function list(int $departmentId, ?string $name)
    {
        return DB::table('provinces')
            ->select(
                'id as province_id',
                'name'
            )
            ->where('department_id', $departmentId)
            ->when($name, fn($q) => $q->where('name', $name))
            ->get();
    }
}