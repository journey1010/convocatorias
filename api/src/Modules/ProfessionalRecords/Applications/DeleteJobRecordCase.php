<?php

namespace Modules\ProfessionalRecords\Applications;

use Illuminate\Support\Facades\DB;
use Modules\ProfessionalRecords\Repositories\JobRecordRepository;
use Modules\ProfessionalRecords\Services\ProfessionalFileStorageService;

class DeleteJobRecordCase
{
    public function __construct(
        private JobRecordRepository $repository,
        private ProfessionalFileStorageService $fileService
    ) {}

    public function exec(int $recordId): void
    {
        DB::transaction(function () use ($recordId) {
            $record = $this->repository->findByIdOrFail($recordId);

            // Delete file if exists
            if ($record->file) {
                $this->fileService->deleteFile($record->file);
            }

            $this->repository->delete($record);
        });
    }
}
