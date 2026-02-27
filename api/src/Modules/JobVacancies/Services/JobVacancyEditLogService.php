<?php

namespace Modules\JobVacancies\Services;

use Modules\JobVacancies\Models\JobVacancy;
use Modules\JobVacancies\Repositories\JobVacancyEditLogRepository;
use Modules\Auth\Infrastructure\Context\RequestContext;

class JobVacancyEditLogService
{
    public function __construct(
        private JobVacancyEditLogRepository $logRepository
    ) {}

    /**
     * Registra un cambio en la convocatoria
     */
    public function logChange(
        JobVacancy $vacancy,
        array $oldData,
        array $newData,
        string $action,
        RequestContext $ctx,
        ?string $ipAddress = null
    ): void {
        // Solo registrar si la convocatoria require logs
        if (!$vacancy->shouldLogChanges()) {
            return;
        }

        $changedFields = array_keys(array_diff_assoc($newData, $oldData));

        $this->logRepository->create([
            'job_vacancy_id' => $vacancy->id,
            'user_id' => $ctx->userId,
            'action' => $action,
            'old_values' => $oldData,
            'new_values' => $newData,
            'changed_fields' => $changedFields,
            'ip_address' => $ipAddress ?? request()->ip(),
        ]);
    }

    /**
     * Registra un cambio de estado
     */
    public function logStatusChange(
        JobVacancy $vacancy,
        int $oldStatus,
        int $newStatus,
        RequestContext $ctx
    ): void {
        $this->logRepository->create([
            'job_vacancy_id' => $vacancy->id,
            'user_id' => $ctx->userId,
            'action' => 'status_changed',
            'old_values' => ['status' => $oldStatus],
            'new_values' => ['status' => $newStatus],
            'changed_fields' => ['status'],
            'ip_address' => request()->ip(),
        ]);
    }
}
