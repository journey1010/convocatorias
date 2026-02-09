<?php

namespace Modules\ProfessionalRecords\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\Models\User;

class JobRecord extends Model
{
    use HasFactory;

    protected $table = 'job_records';

    // Type enum constants
    public const TYPE_PRIVATE = 1;
    public const TYPE_PUBLIC = 2;

    // Status enum constants
    public const STATUS_IN_PROGRESS = 1;
    public const STATUS_FINISHED = 2;

    protected $fillable = [
        'user_id',
        'entity_name',
        'type',
        'specialization_area',
        'status',
        'description',
        'start_date',
        'end_date',
        'file',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'type' => 'integer',
            'status' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
