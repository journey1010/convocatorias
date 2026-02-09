<?php

namespace Modules\ProfessionalRecords\Applications;

use Illuminate\Support\Facades\DB;
use Modules\ProfessionalRecords\Applications\Dtos\{CreateCertificationDto, CertificationResponseDto};
use Modules\ProfessionalRecords\Repositories\CertificationRepository;
use Modules\ProfessionalRecords\Services\ProfessionalFileStorageService;

class CreateCertificationCase
{
    public function __construct(
        private CertificationRepository $repository,
        private ProfessionalFileStorageService $fileService
    ) {}

    public function exec(CreateCertificationDto $dto): CertificationResponseDto
    {
        return DB::transaction(function () use ($dto) {
            $this->fileService->user_id = (string) $dto->user_id;

            $filePath = $this->fileService->storeFile($dto->file, 'certification');

            $data = [
                'user_id' => $dto->user_id,
                'name' => $dto->name,
                'year' => $dto->year,
                'hours' => $dto->hours,
                'file' => $filePath,
            ];

            $record = $this->repository->create($data);

            return CertificationResponseDto::fromModel($record);
        });
    }
}
