<?php

namespace Modules\Accounts\Applications;

use Illuminate\Support\Facades\DB;
use Modules\Accounts\Applications\Dtos\{UpdatePersonalDataExtraDto, PersonalDataExtraResponseDto};
use Modules\Accounts\Repositories\PersonalDataExtraRepository;
use Modules\Accounts\Services\FileStorageService;
use Infrastructure\Exceptions\JsonResponseException;

class UpdatePersonalDataExtraCase
{
    public function __construct(
        private PersonalDataExtraRepository $repository,
        private FileStorageService $fileService,
    ) {}

    public function exec(UpdatePersonalDataExtraDto $dto, int $authenticatedUserId): PersonalDataExtraResponseDto
    {
        // Get the personal data
        $personalData = $this->repository->getByUserIdOrFail($dto->user_id);

        // Verify that the user can only update their own personal data
        if ($personalData->user_id !== $authenticatedUserId) {
            throw new JsonResponseException('No tienes permiso para modificar la información de otro usuario', 403);
        }

        return DB::transaction(function () use ($personalData, $dto) {
            $data = [
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

            // Update disability certificate if provided
            if ($dto->file_cert_disability) {
                $data['file_cert_disability'] = $this->fileService->updateCertificate(
                    $personalData->file_cert_disability,
                    $dto->file_cert_disability,
                    'disability'
                );
            } elseif (!$dto->have_cert_disability) {
                $this->fileService->deleteCertificate($personalData->file_cert_disability);
                $data['file_cert_disability'] = null;
            }

            // Update army certificate if provided
            if ($dto->file_cert_army) {
                $data['file_cert_army'] = $this->fileService->updateCertificate(
                    $personalData->file_cert_army,
                    $dto->file_cert_army,
                    'army'
                );
            } elseif (!$dto->have_cert_army) {
                $this->fileService->deleteCertificate($personalData->file_cert_army);
                $data['file_cert_army'] = null;
            }

            // Update professional credentials certificate if provided
            if ($dto->file_cert_professional_credentials) {
                $data['file_cert_professional_credentials'] = $this->fileService->updateCertificate(
                    $personalData->file_cert_professional_credentials,
                    $dto->file_cert_professional_credentials,
                    'professional_credentials'
                );
            } elseif (!$dto->have_cert_professional_credentials) {
                $this->fileService->deleteCertificate($personalData->file_cert_professional_credentials);
                $data['file_cert_professional_credentials'] = null;
            }

            $updated = $this->repository->update($personalData, $data);

            return PersonalDataExtraResponseDto::fromModel($updated);
        });
    }
}
