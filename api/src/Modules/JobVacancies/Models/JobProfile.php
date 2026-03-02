<?php

namespace Modules\JobVacancies\Models;

use Modules\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Modules\Office\Models\{Locale, Office};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\ProfessionalRecords\Models\SpecializationArea;

class JobProfile extends Model
{
    use HasFactory;

    protected $table = 'job_profiles';

    protected $fillable = [
        'locale_id',
        'created_by',
        'job_vacancy_id',
        'title',
        'salary',
        'office_id',
        'code_profile',
        'specialization_area_id',
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

    public function specializationArea(): BelongsTo
    {
        return $this->belongsTo(SpecializationArea::class, 'specialization_area_id');
    }
}