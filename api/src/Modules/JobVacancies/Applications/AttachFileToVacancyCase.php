<?php

namespace Modules\JobVacancies\Applications;

use Illuminate\Support\Facades\DB;
use Modules\JobVacancies\Applications\Dtos\JobVacancyFileDto;
use Modules\JobVacancies\Repositories\{JobVacancyRepository, JobVacancyFileRepository};
use Modules\JobVacancies\Services\{JobVacancyFileStorageService, JobVacancyEditLogService};
use Modules\Auth\Infrastructure\Context\RequestContext;
use Infrastructure\Exceptions\JsonResponseException;

class AttachFileToVacancyCase
{
    public function __construct(
        private JobVacancyRepository $vacancyRepository,
        private JobVacancyFileRepository $fileRepository,
        private JobVacancyFileStorageService $storageService,
        private JobVacancyEditLogService $logService
    ) {}

    public function exec(JobVacancyFileDto $dto, RequestContext $ctx): array
    {
        return DB::transaction(function () use ($dto, $ctx) {
            $vacancy = $this->vacancyRepository->findByIdOrFail($dto->job_vacancy_id);

            // Verificar permisos
            if (!$this->vacancyRepository->canUserEdit($vacancy, $ctx)) {
                throw new JsonResponseException('No tienes permiso para adjuntar archivos a esta convocatoria', 403);
            }

            // Verificar si se pueden adjuntar archivos
            if (!$vacancy->canAttachFiles()) {
                throw new JsonResponseException(
                    'No se pueden adjuntar archivos a una convocatoria ' . $vacancy->status->name,
                    422
                );
            }

            // Almacenar el archivo
            $this->storageService->user_id = (string) $ctx->userId;
            $filePath = $this->storageService->storeVacancyFile($dto->file, 'attachments');

            // Crear registro del archivo
            $file = $this->fileRepository->create([
                'locale_id' => $vacancy->locale_id,
                'job_vancancy_id' => $vacancy->id, // Mantener typo de la migración
                'file' => $filePath,
                'name' => $dto->name,
            ]);

            // Registrar en el log
            if ($vacancy->shouldLogChanges()) {
                $this->logService->logChange(
                    $vacancy,
                    [],
                    ['file_attached' => $dto->name],
                    'file_attached',
                    $ctx
                );
            }

            return [
                'id' => $file->id,
                'name' => $file->name,
                'file' => $file->file,
                'created_at' => $file->created_at->toISOString(),
            ];
        });
    }
}
