<?php

namespace Modules\JobVacancies\Applications;

use Illuminate\Support\Facades\DB;
use Modules\JobVacancies\Applications\Dtos\{UpdateJobVacancyDto, JobVacancyResponseDto};
use Modules\JobVacancies\Repositories\JobVacancyRepository;
use Modules\JobVacancies\Services\JobVacancyEditLogService;
use Modules\Auth\Infrastructure\Context\RequestContext;
use Infrastructure\Exceptions\JsonResponseException;

class UpdateJobVacancyCase
{
    public function __construct(
        private JobVacancyRepository $vacancyRepository,
        private JobVacancyEditLogService $logService
    ) {}

    public function exec(UpdateJobVacancyDto $dto, RequestContext $ctx): JobVacancyResponseDto
    {
        return DB::transaction(function () use ($dto, $ctx) {
            $vacancy = $this->vacancyRepository->findByIdOrFail($dto->id);

            // Verificar permisos
            if (!$this->vacancyRepository->canUserEdit($vacancy, $ctx)) {
                throw new JsonResponseException('No tienes permiso para editar esta convocatoria', 403);
            }

            // Verificar si la convocatoria puede ser editada
            if (!$vacancy->isEditable()) {
                throw new JsonResponseException(
                    'No se puede editar una convocatoria en estado ' . $vacancy->status->name,
                    422
                );
            }

            // Guardar valores antiguos para el log
            $oldData = $vacancy->only(['title', 'status', 'mode', 'start_date', 'close_date']);

            // Preparar datos de actualización
            $updateData = [];
            if ($dto->title !== null) $updateData['title'] = $dto->title;
            if ($dto->status !== null) $updateData['status'] = $dto->status;
            if ($dto->mode !== null) $updateData['mode'] = $dto->mode;
            if ($dto->start_date !== null) $updateData['start_date'] = $dto->start_date;
            if ($dto->close_date !== null) $updateData['close_date'] = $dto->close_date;

            // Actualizar
            $vacancy = $this->vacancyRepository->update($vacancy, $updateData);

            // Registrar cambios en el log
            $newData = $vacancy->only(['title', 'status', 'mode', 'start_date', 'close_date']);
            $this->logService->logChange($vacancy, $oldData, $newData, 'updated', $ctx);

            return JobVacancyResponseDto::fromModel($vacancy);
        });
    }
}
