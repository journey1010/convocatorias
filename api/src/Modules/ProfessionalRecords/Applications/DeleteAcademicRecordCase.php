<?php

namespace Modules\ProfessionalRecords\Applications;

use Illuminate\Support\Facades\DB;
use Modules\ProfessionalRecords\Repositories\AcademicRecordRepository;
use Modules\ProfessionalRecords\Services\ProfessionalFileStorageService;

class DeleteAcademicRecordCase
{
    public function __construct(
        private AcademicRecordRepository $repository,
        private ProfessionalFileStorageService $fileService
    ) {}

    public function exec(int $recordId): void
    {
        DB::transaction(function () use ($recordId) {
            $record = $this->repository->findByIdOrFail($recordId);

            // Delete the file
            $this->fileService->deleteFile($record->file);

            // Delete the record
            $this->repository->delete($record);
        });
    }
}
