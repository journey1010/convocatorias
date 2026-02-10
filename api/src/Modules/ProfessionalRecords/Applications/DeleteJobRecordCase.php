<?php

namespace Modules\ProfessionalRecords\Applications;

use Illuminate\Support\Facades\DB;
use Modules\ProfessionalRecords\Repositories\JobRecordRepository;
use Modules\ProfessionalRecords\Services\ProfessionalFileStorageService;
use Infrastructure\Exceptions\JsonResponseException;

class DeleteJobRecordCase
{
    public function __construct(
        private JobRecordRepository $repository,
        private ProfessionalFileStorageService $fileService
    ) {}

    public function exec(int $recordId, int $userId): void
    {
        DB::transaction(function () use ($recordId, $userId) {
            $record = $this->repository->findByIdOrFail($recordId);

            if($record->user_id !== $userId){
                throw new JsonResponseException('No tienes permiso para eliminar este registro', 403);
            }

            if ($record->file) {
                $this->fileService->deleteFile($record->file);
            }

            $this->repository->delete($record);
        });
    }
}
