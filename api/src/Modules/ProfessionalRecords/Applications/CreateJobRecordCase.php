<?php

namespace Modules\ProfessionalRecords\Applications;

use Illuminate\Support\Facades\DB;
use Modules\ProfessionalRecords\Applications\Dtos\{CreateJobRecordDto, JobRecordResponseDto};
use Modules\ProfessionalRecords\Repositories\JobRecordRepository;
use Modules\ProfessionalRecords\Services\ProfessionalFileStorageService;

class CreateJobRecordCase
{
    public function __construct(
        private JobRecordRepository $repository,
        private ProfessionalFileStorageService $fileService
    ) {}

    public function exec(CreateJobRecordDto $dto): JobRecordResponseDto
    {
        return DB::transaction(function () use ($dto) {
            $this->fileService->user_id = (string) $dto->user_id;

            $data = [
                'user_id' => $dto->user_id,
                'entity_name' => $dto->entity_name,
                'type' => $dto->type,
                'specialization_area' => $dto->specialization_area,
                'status' => $dto->status,
                'description' => $dto->description,
                'start_date' => $dto->start_date,
                'end_date' => $dto->end_date,
            ];

            // File is optional for job records
            if ($dto->file) {
                $data['file'] = $this->fileService->storeFile($dto->file, 'job');
            }

            $record = $this->repository->create($data);

            return JobRecordResponseDto::fromModel($record);
        });
    }
}
