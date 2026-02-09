<?php

namespace Modules\ProfessionalRecords\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpecializationArea extends Model
{
    use HasFactory;

    protected $table = 'specialization_areas';

    protected $fillable = [
        'name',
    ];

    public function academicRecords(): HasMany
    {
        return $this->hasMany(AcademicRecord::class);
    }
}
