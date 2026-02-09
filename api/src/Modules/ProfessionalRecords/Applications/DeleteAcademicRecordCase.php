<?php

namespace Modules\ProfessionalRecords\Applications;

use Illuminate\Support\Facades\DB;
use Modules\ProfessionalRecords\Repositories\AcademicRecordRepository;
use Modules\ProfessionalRecords\Services\ProfessionalFileStorageService;
use Infrastructure\Exceptions\JsonResponseException;

class DeleteAcademicRecordCase
{
    public function __construct(
        private AcademicRecordRepository $repository,
        private ProfessionalFileStorageService $fileService
    ) {}

    public function exec(int $recordId, int $userId): void
    {
        DB::transaction(function () use ($recordId, $userId) {
            $record = $this->repository->findByIdOrFail($recordId);

            if($record->user_id !== $userId){
                throw new JsonResponseException('Unauthorized', 401);
            }
            
            $this->fileService->deleteFile($record->file);

            $this->repository->delete($record);
        });
    }
}
