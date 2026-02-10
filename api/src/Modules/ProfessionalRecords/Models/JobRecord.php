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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
