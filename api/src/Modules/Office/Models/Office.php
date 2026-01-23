<?php

namespace Modules\Office\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Office extends Model {

    protected  $fillable = [
        'name', 
        'status', 
        'level'
    ];

    public static function lister(int $level, ?int $itemPerPage, ?int $page, ?string $name): array
    {
        $query = DB::table('offices as o')
            ->select(
                'o.id', 
                'o.name', 
                'o.status'
            )
            ->where('o.status', 1)
            ->where('o.level', '>=', $level)
            ->where('o.name', 'like', "%$name%");

        if($itemPerPage && $page){
            $offices = $query->paginate($itemPerPage, ['*'], 'page', $page);
            return [
                'items' => $offices->items(),
                'total' => $offices->total(),
            ];
        }
        
        return [
            'items' => $query->get(),
        ];
    }
}