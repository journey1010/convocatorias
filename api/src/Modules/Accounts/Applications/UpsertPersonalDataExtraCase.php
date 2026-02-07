<?php

namespace Modules\Accounts\Applications;

use Illuminate\Support\Facades\DB;
use Modules\Accounts\Applications\Dtos\{StorePersonalDataExtraDto, PersonalDataExtraResponseDto};
use Modules\Accounts\Repositories\PersonalDataExtraRepository;
use Modules\Accounts\Services\FileStorageService;

class UpsertPersonalDataExtraCase
{
    public function __construct(private PersonalDataExtraRepository $repository, private FileStorageService $fileService) {}

    public function exec(StorePersonalDataExtraDto $dto): PersonalDataExtraResponseDto
    {
        $existingPersonalData = $this->repository->findByUserId($dto->user_id);

        if ($existingPersonalData) {
            return $this->updateExistingData($existingPersonalData, $dto);
        }

        // Create new record if it doesn't exist
        return $this->createNewData($dto);
    }

    private function createNewData(StorePersonalDataExtraDto $dto): PersonalDataExtraResponseDto
    {
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

    private function updateExistingData($personalData, StorePersonalDataExtraDto $dto): PersonalDataExtraResponseDto
    {
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