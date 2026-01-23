<?php

namespace Modules\User\Models; 

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OfficeUser extends Model {
    
    protected $fillable = [
        'office_id', 
        'user_id', 
    ];

    public static function updateData(int $user, int|array $office)
    {
        $office = is_array($office) ? $office : [$office]; 

        static::where('user_id', $user)->delete();

        $data = [];
        $now = now();
        foreach ($office as $off) {
            $data[] = [
                'user_id'   => $user,
                'office_id' => $off,
                'created_at'=> $now,
                'updated_at'=> $now
            ];
        }

        if (!empty($data)) {
            DB::table('office_users')->insert($data);
        }
    }

    public static function get(int $user_id): Collection
    {
        return DB::table('office_users as ou')
            ->select('o.id', 'o.name')
            ->join('offices as o', 'o.id', '=', 'ou.office_id')
            ->where('ou.user_id', $user_id)
            ->orderBy('o.id')
            ->get();
    }
}   