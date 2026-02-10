<?php

namespace Modules\ProfessionalRecords\Applications;

use Illuminate\Support\Facades\DB;
use Modules\ProfessionalRecords\Applications\Dtos\{UpdateCertificationDto, CertificationResponseDto};
use Modules\ProfessionalRecords\Repositories\CertificationRepository;
use Modules\ProfessionalRecords\Services\ProfessionalFileStorageService;
use Infrastructure\Exceptions\JsonResponseException;

class UpdateCertificationCase
{
    public function __construct(
        private CertificationRepository $repository,
        private ProfessionalFileStorageService $fileService
    ) {}

    public function exec(int $recordId, int $userId, UpdateCertificationDto $dto): CertificationResponseDto
    {
        return DB::transaction(function () use ($recordId, $userId, $dto) {
            $record = $this->repository->findByIdOrFail($recordId);

            if ($record->user_id != $userId) {
                throw new JsonResponseException('No tienes permiso para actualizar este registro', 403);
            }

            $this->fileService->user_id = (string) $userId;

            $data = [
                'name' => $dto->name,
                'year' => $dto->year,
                'hours' => $dto->hours,
            ];

            if ($dto->file) {
                $data['file'] = $this->fileService->updateFile(
                    $record->file,
                    $dto->file,
                    'certification'
                );
            }

            $updated = $this->repository->update($record, $data);

            return CertificationResponseDto::fromModel($updated);
        });
    }
}
