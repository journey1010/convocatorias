<?php

namespace Modules\Office\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Office extends Model {

    use HasFactory;    

    protected static function newFactory()
    {
        return \Database\Factories\OfficeFactory::new();
    }

    protected  $fillable = [
        'name', 
        'locale_id',
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