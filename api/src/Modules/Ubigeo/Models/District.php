<?php

namespace Modules\Ubigeo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class District extends Model
{
    protected $fillable = [
        'province_id',
        'district_id',
        'name',
    ];

    public static function list(int $province_id, ?string $name)
    {
        return DB::table('districts')
            ->where('province_id', $province_id)
            ->when($name, fn($q) => $q->where('name', 'like', "%$name%"))
            ->select(
                'id as district_id', 
                'name',
            )
            ->get();
    }
}