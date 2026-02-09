<?php

namespace Modules\ProfessionalRecords\Applications;

use Illuminate\Support\Facades\DB;
use Modules\ProfessionalRecords\Repositories\CertificationRepository;
use Modules\ProfessionalRecords\Services\ProfessionalFileStorageService;

class DeleteCertificationCase
{
    public function __construct(
        private CertificationRepository $repository,
        private ProfessionalFileStorageService $fileService
    ) {}

    public function exec(int $recordId): void
    {
        DB::transaction(function () use ($recordId) {
            $record = $this->repository->findByIdOrFail($recordId);

            $this->fileService->deleteFile($record->file);
            $this->repository->delete($record);
        });
    }
}
