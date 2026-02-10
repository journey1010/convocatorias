<?php

namespace Modules\ProfessionalRecords\Applications;

use JsonException;
use Illuminate\Support\Facades\DB;
use Modules\ProfessionalRecords\Applications\Dtos\{UpdateJobRecordDto, JobRecordResponseDto};
use Modules\ProfessionalRecords\Repositories\JobRecordRepository;
use Modules\ProfessionalRecords\Services\ProfessionalFileStorageService;

class UpdateJobRecordCase
{
    public function __construct(
        private JobRecordRepository $repository,
        private ProfessionalFileStorageService $fileService
    ) {}

    public function exec(int $userId, UpdateJobRecordDto $dto): JobRecordResponseDto
    {
        return DB::transaction(function () use ($userId, $dto) {
            $record = $this->repository->findByIdOrFail($dto->id);

            if($record->user_id !== $userId){
                throw new JsonException('Unauthorized', 403);
            }

            $this->fileService->user_id = (string) $userId;

            $data = [
                'entity_name' => $dto->entity_name,
                'type' => $dto->type,
                'specialization_area' => $dto->specialization_area,
                'status' => $dto->status,
                'description' => $dto->description,
                'start_date' => $dto->start_date,
                'end_date' => $dto->end_date,
            ];

            if ($dto->file) {
                $data['file'] = $this->fileService->updateFile(
                    $record->file,
                    $dto->file,
                    'job'
                );
            }

            $updated = $this->repository->update($record, $data);

            return JobRecordResponseDto::fromModel($updated);
        });
    }
}