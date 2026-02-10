<?php

namespace Modules\ProfessionalRecords\Applications;

use Illuminate\Support\Facades\DB;
use Modules\ProfessionalRecords\Repositories\CertificationRepository;
use Modules\ProfessionalRecords\Services\ProfessionalFileStorageService;
use Infrastructure\Exceptions\JsonResponseException;

class DeleteCertificationCase
{
    public function __construct(
        private CertificationRepository $repository,
        private ProfessionalFileStorageService $fileService
    ) {}

    public function exec(int $recordId, int $userId): void
    {
        DB::transaction(function () use ($recordId, $userId) {
            $record = $this->repository->findByIdOrFail($recordId);

            if ($record->user_id != $userId) {
                throw new JsonResponseException('No tienes permiso para eliminar este registro', 403);
            }

            $this->fileService->deleteFile($record->file);
            $this->repository->delete($record);
        });
    }
}
