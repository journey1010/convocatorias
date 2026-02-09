<?php

namespace Modules\ProfessionalRecords\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\Models\User;
use Modules\ProfessionalRecords\Enums\{AcademicLevel, AcademicStatus};

class AcademicRecord extends Model
{
    use HasFactory;

    protected $table = 'academic_records';

    protected $fillable = [
        'user_id',
        'specialization_area_id',
        'level',
        'status',
        'start_date',
        'end_date',
        'description',
        'file',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function specializationArea(): BelongsTo
    {
        return $this->belongsTo(SpecializationArea::class);
    }
}