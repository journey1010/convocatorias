<?php

namespace Modules\ProfessionalRecords\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\Models\User;

class AcademicRecord extends Model
{
    use HasFactory;

    protected $table = 'academic_records';

    // Status enum constants
    public const STATUS_COMPLETED = 1;
    public const STATUS_IN_PROGRESS = 2;
    public const STATUS_INCOMPLETE = 3;

    // Level enum constants
    public const LEVEL_PRIMARY = 0;
    public const LEVEL_SECONDARY = 1;
    public const LEVEL_TECHNICAL = 2;
    public const LEVEL_UNIVERSITY = 3;
    public const LEVEL_MASTER = 4;
    public const LEVEL_DOCTORATE = 5;

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

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'status' => 'integer',
            'level' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function specializationArea(): BelongsTo
    {
        return $this->belongsTo(SpecializationArea::class);
    }
}
