<?php

namespace Modules\JobVacancies\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\Models\User;

class JobVacancyEditLog extends Model
{
    use HasFactory;

    protected $table = 'job_vacancy_edit_logs';

    protected $fillable = [
        'job_vacancy_id',
        'user_id',
        'action',
        'old_values',
        'new_values',
        'changed_fields',
        'ip_address',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'changed_fields' => 'array',
    ];

    // Relationships
    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(JobVacancy::class, 'job_vacancy_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
