<?php

namespace Modules\Accounts\Applications;

use Illuminate\Support\Facades\DB;
use Modules\Accounts\Applications\Dtos\{StorePersonalDataExtraDto, PersonalDataExtraResponseDto};
use Modules\Accounts\Repositories\PersonalDataExtraRepository;
use Modules\Accounts\Services\FileStorageService;
use Infrastructure\Exceptions\JsonResponseException;

class StorePersonalDataExtraCase
{
    public function __construct(
        private PersonalDataExtraRepository $repository,
        private FileStorageService $fileService,
    ) {}

    public function exec(StorePersonalDataExtraDto $dto, int $authenticatedUserId): PersonalDataExtraResponseDto
    {
        // Verify that the user can only create their own personal data
        if ($dto->user_id !== $authenticatedUserId) {
            throw new JsonResponseException('No tienes permiso para crear información de otro usuario', 403);
        }

        // Verify that the user doesn't already have personal data
        if ($this->repository->findByUserId($dto->user_id)) {
            throw new JsonResponseException('Este usuario ya tiene información personal registrada', 409);
        }

        return DB::transaction(function () use ($dto) {
            $data = [
                'user_id' => $dto->user_id,
                'department_id' => $dto->department_id,
                'province_id' => $dto->province_id,
                'district_id' => $dto->district_id,
                'address' => $dto->address,
                'birthday' => $dto->birthday,
                'genere' => $dto->genere,
                'have_cert_disability' => $dto->have_cert_disability,
                'have_cert_army' => $dto->have_cert_army,
                'have_cert_professional_credentials' => $dto->have_cert_professional_credentials,
                'is_active_cert_professional_credentials' => $dto->is_active_cert_professional_credentials,
            ];

            // Store certificates if provided
            if ($dto->have_cert_disability && $dto->file_cert_disability) {
                $data['file_cert_disability'] = $this->fileService->storeCertificate(
                    $dto->file_cert_disability,
                    'disability'
                );
            }

            if ($dto->have_cert_army && $dto->file_cert_army) {
                $data['file_cert_army'] = $this->fileService->storeCertificate(
                    $dto->file_cert_army,
                    'army'
                );
            }

            if ($dto->have_cert_professional_credentials && $dto->file_cert_professional_credentials) {
                $data['file_cert_professional_credentials'] = $this->fileService->storeCertificate(
                    $dto->file_cert_professional_credentials,
                    'professional_credentials'
                );
            }

            $personalData = $this->repository->create($data);

            return PersonalDataExtraResponseDto::fromModel($personalData);
        });
    }
}
