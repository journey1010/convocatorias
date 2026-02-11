<?php

namespace Modules\JobVacancies\Applications;

use Modules\JobVacancies\Repositories\JobVacancyRepository;
use Modules\JobVacancies\Services\JobVacancyEditLogService;
use Modules\Auth\Infrastructure\Context\RequestContext;
use Modules\JobVacancies\Applications\Dtos\JobVacancyResponseDto;
use Infrastructure\Exceptions\JsonResponseException;

class UpdateVacancyStatusCase
{
    public function __construct(
        private JobVacancyRepository $vacancyRepository,
        private JobVacancyEditLogService $logService
    ) {}

    public function exec(int $vacancyId, int $newStatus, RequestContext $ctx): JobVacancyResponseDto
    {
        $vacancy = $this->vacancyRepository->findByIdOrFail($vacancyId);

        // Verificar permisos
        if (!$this->vacancyRepository->canUserEdit($vacancy, $ctx)) {
            throw new JsonResponseException('No tienes permiso para cambiar el estado de esta convocatoria', 403);
        }

        $oldStatus = $vacancy->status->value;

        // Actualizar estado
        $vacancy = $this->vacancyRepository->update($vacancy, ['status' => $newStatus]);

        // Siempre registrar cambios de estado
        $this->logService->logStatusChange($vacancy, $oldStatus, $newStatus, $ctx);

        return JobVacancyResponseDto::fromModel($vacancy);
    }
}
