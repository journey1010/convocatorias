<?php

namespace Modules\JobVacancies\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\Models\User;
use Modules\Office\Models\{Locale, Office};

class JobProfile extends Model
{
    use HasFactory;

    protected $table = 'job_profiles';

    protected $fillable = [
        'locale_id',
        'user_id',
        'job_vacancy_id',
        'title',
        'salary',
        'office_id',
        'code_profile',
        'file',
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

    public function locale(): BelongsTo
    {
        return $this->belongsTo(Locale::class);
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }
}
