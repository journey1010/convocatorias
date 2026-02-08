<?php

namespace Modules\Office\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Locale extends Model {

    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\LocaleFactory::new();
    }

    public function Office()
    {
        return $this->hasMany(\Modules\Office\Models\Office::class);
    }
    
    protected  $fillable = [
        'name'
    ];
}