<?php

namespace Modules\JobVacancies\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Modules\User\Models\User;
use Modules\Office\Models\Locale;
use Modules\JobVacancies\Enums\{VacancyStatus, ApplicationMode};

class JobVacancy extends Model
{
    use HasFactory;

    protected $table = 'job_vacancies';

    protected $fillable = [
        'user_id',
        'locale_id',
        'title',
        'status',
        'mode',
        'start_date',
        'close_date',
    ];

    protected $casts = [
        'status' => VacancyStatus::class,
        'mode' => 'boolean',
        'start_date' => 'date',
        'close_date' => 'date',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function locale(): BelongsTo
    {
        return $this->belongsTo(Locale::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(JobVacancyFile::class);
    }

    public function profiles(): HasMany
    {
        return $this->hasMany(JobProfile::class);
    }

    public function editLogs(): HasMany
    {
        return $this->hasMany(JobVacancyEditLog::class);
    }

    // Business Logic Methods
    
    /**
     * Verifica si la convocatoria puede ser editada (datos generales y perfiles)
     */
    public function isEditable(): bool
    {
        return $this->status !== VacancyStatus::EN_EVALUACION
            && $this->status !== VacancyStatus::FINALIZADA
            && $this->status !== VacancyStatus::CANCELADA;
    }

    /**
     * Verifica si se pueden adjuntar archivos
     */
    public function canAttachFiles(): bool
    {
        // Siempre se pueden adjuntar archivos, incluso en evaluación
        return $this->status !== VacancyStatus::FINALIZADA
            && $this->status !== VacancyStatus::CANCELADA;
    }

    /**
     * Verifica si se pueden editar perfiles
     */
    public function canUpdateProfiles(): bool
    {
        return $this->isEditable();
    }

    /**
     * Verifica si se deben registrar logs al editar
     * No se registran logs cuando está en estado PUBLICADA
     */
    public function shouldLogChanges(): bool
    {
        return $this->status !== VacancyStatus::PUBLICADA;
    }

    /**
     * Verifica si se pueden editar nombres de archivos antiguos
     * Solo se pueden editar archivos nuevos cuando está en evaluación
     */
    public function canEditOldFileNames(): bool
    {
        return $this->status !== VacancyStatus::EN_EVALUACION;
    }
}
