<?php

namespace Modules\JobVacancies\Applications;

use Modules\JobVacancies\Repositories\{JobVacancyRepository, JobVacancyFileRepository};
use Modules\JobVacancies\Services\JobVacancyEditLogService;
use Modules\Auth\Infrastructure\Context\RequestContext;
use Infrastructure\Exceptions\JsonResponseException;

class UpdateVacancyFileNameCase
{
    public function __construct(
        private JobVacancyRepository $vacancyRepository,
        private JobVacancyFileRepository $fileRepository,
        private JobVacancyEditLogService $logService
    ) {}

    public function exec(int $fileId, string $newName, RequestContext $ctx): array
    {
        $file = $this->fileRepository->findByIdOrFail($fileId);
        $vacancy = $this->vacancyRepository->findByIdOrFail($file->job_vancancy_id);

        // Verificar permisos
        if (!$this->vacancyRepository->canUserEdit($vacancy, $ctx)) {
            throw new JsonResponseException('No tienes permiso para editar archivos de esta convocatoria', 403);
        }

        // Verificar si se puede editar el nombre del archivo
        // Si está en evaluación, solo se pueden editar archivos nuevos (simplificado: permitir todos por ahora)
        $oldName = $file->name;
        
        $file = $this->fileRepository->update($file, ['name' => $newName]);

        // Registrar en el log
        if ($vacancy->shouldLogChanges()) {
            $this->logService->logChange(
                $vacancy,
                ['file_name' => $oldName],
                ['file_name' => $newName],
                'file_name_updated',
                $ctx
            );
        }

        return [
            'id' => $file->id,
            'name' => $file->name,
            'file' => $file->file,
        ];
    }
}
