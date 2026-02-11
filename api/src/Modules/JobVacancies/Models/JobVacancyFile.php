<?php

namespace Modules\JobVacancies\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Office\Models\Locale;

class JobVacancyFile extends Model
{
    use HasFactory;

    protected $table = 'job_vacancy_files';

    protected $fillable = [
        'locale_id',
        'job_vancancy_id', // Note: keeping typo from migration
        'file',
        'name',
    ];

    // Relationships
    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(JobVacancy::class, 'job_vancancy_id');
    }

    public function locale(): BelongsTo
    {
        return $this->belongsTo(Locale::class);
    }
}
