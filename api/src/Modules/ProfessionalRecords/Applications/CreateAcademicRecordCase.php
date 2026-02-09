<?php

namespace Modules\ProfessionalRecords\Applications;

use Illuminate\Support\Facades\DB;
use Modules\ProfessionalRecords\Applications\Dtos\{CreateAcademicRecordDto, AcademicRecordResponseDto};
use Modules\ProfessionalRecords\Repositories\AcademicRecordRepository;
use Modules\ProfessionalRecords\Services\ProfessionalFileStorageService;

class CreateAcademicRecordCase
{
    public function __construct(
        private AcademicRecordRepository $repository,
        private ProfessionalFileStorageService $fileService
    ) {}

    public function exec(CreateAcademicRecordDto $dto): AcademicRecordResponseDto
    {
        return DB::transaction(function () use ($dto) {
            $this->fileService->user_id = (string) $dto->user_id;

            $filePath = $this->fileService->storeFile($dto->file, 'academic');

            $data = [
                'user_id' => $dto->user_id,
                'specialization_area_id' => $dto->specialization_area_id,
                'level' => $dto->level,
                'status' => $dto->status,
                'start_date' => $dto->start_date,
                'end_date' => $dto->end_date,
                'description' => $dto->description,
                'file' => $filePath,
            ];

            $record = $this->repository->create($data);

            return AcademicRecordResponseDto::fromModel($record);
        });
    }
}
