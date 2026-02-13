<?php

namespace Modules\Accounts\Applications;

use Illuminate\Support\Facades\DB;
use Modules\Accounts\Applications\Dtos\{StorePersonalDataExtraDto, PersonalDataExtraResponseDto};
use Modules\Accounts\Repositories\PersonalDataExtraRepository;
use Modules\Accounts\Services\FileStorageService;
use Modules\Accounts\Models\PersonalDataExtra;

class UpsertPersonalDataExtraCase
{
    public function __construct(private PersonalDataExtraRepository $repository, private FileStorageService $fileService) {}

    public function exec(StorePersonalDataExtraDto $dto): PersonalDataExtraResponseDto
    {
        $model = $this->repository->findByUserId($dto->user_id);
        $this->fileService->user_id = $dto->user_id;

        return DB::transaction(function () use ($dto, $model) {
            $data = $dto->toArray();

            $fileFields = [
                'file_dni' => 'dni',
                'file_cert_disability' => 'disability',
                'file_cert_army' => 'army',
                'file_cert_professional_credentials' => 'professional_credentials',
            ];

            foreach ($fileFields as $field => $type) {
                $data[$field] = $this->processFile($model, $dto, $field, $type);
            }

            $result = $model 
                ? $this->repository->update($model, $data)
                : $this->repository->create(array_merge($data, ['user_id' => $dto->user_id]));

            return PersonalDataExtraResponseDto::fromModel($result);
        });
    }

    private function processFile(?PersonalDataExtra $model, StorePersonalDataExtraDto $dto, string $field, string $type): ?string
    {
        $newFile = $dto->{$field};
        $oldPath = $model?->{$field};
        
        if ($newFile) {
            return $model 
                ? $this->fileService->updateCertificate($oldPath, $newFile, $type)
                : $this->fileService->storeCertificate($newFile, $type);
        }

        /**
         * Determina dinámicamente el nombre del campo de control (booleano) en el DTO 
         * y gestiona la eliminación del archivo si la condición es falsa.
         */
        $conditionField = str_replace(['file_', 'cert_'], ['', 'have_cert_'], $field);

        if (isset($dto->{$conditionField}) && $dto->{$conditionField} === false) {
            if ($oldPath) {
                $this->fileService->deleteCertificate($oldPath);
            }
            return null;
        }

        return $oldPath;
    }
}