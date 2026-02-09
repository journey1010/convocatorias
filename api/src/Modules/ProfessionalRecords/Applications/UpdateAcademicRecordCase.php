<?php

namespace Modules\ProfessionalRecords\Applications;

use Illuminate\Support\Facades\DB;
use Modules\ProfessionalRecords\Applications\Dtos\{UpdateAcademicRecordDto, AcademicRecordResponseDto};
use Modules\ProfessionalRecords\Repositories\AcademicRecordRepository;
use Modules\ProfessionalRecords\Services\ProfessionalFileStorageService;

class UpdateAcademicRecordCase
{
    public function __construct(
        private AcademicRecordRepository $repository,
        private ProfessionalFileStorageService $fileService
    ) {}

    public function exec(int $recordId, int $userId, UpdateAcademicRecordDto $dto): AcademicRecordResponseDto
    {
        return DB::transaction(function () use ($recordId, $userId, $dto) {
            $record = $this->repository->findByIdOrFail($recordId);

            $this->fileService->user_id = (string) $userId;

            $data = [
                'specialization_area_id' => $dto->specialization_area_id,
                'level' => $dto->level,
                'status' => $dto->status,
                'start_date' => $dto->start_date,
                'end_date' => $dto->end_date,
                'description' => $dto->description,
            ];

            // Update file if provided
            if ($dto->file) {
                $data['file'] = $this->fileService->updateFile(
                    $record->file,
                    $dto->file,
                    'academic'
                );
            }

            $updated = $this->repository->update($record, $data);

            return AcademicRecordResponseDto::fromModel($updated);
        });
    }
}
