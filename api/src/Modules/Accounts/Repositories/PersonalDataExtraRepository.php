<?php

namespace Modules\Accounts\Repositories;

use Modules\Accounts\Models\PersonalDataExtra;
use Infrastructure\Exceptions\JsonResponseException;

class PersonalDataExtraRepository
{
    public function findByUserId(int $user_id): ?PersonalDataExtra
    {
        return PersonalDataExtra::where('user_id', $user_id)->first();
    }

    public function create(array $data): PersonalDataExtra
    {
        return PersonalDataExtra::create($data);
    }

    public function update(PersonalDataExtra $personalData, array $data): PersonalDataExtra
    {
        $personalData->update($data);
        return $personalData->fresh();
    }

    public function getByUserIdOrFail(int $user_id): PersonalDataExtra
    {
        $personalData = $this->findByUserId($user_id);
        
        if (!$personalData) {
            throw new JsonResponseException('Información personal no encontrada', 404);
        }

        return $personalData;
    }

    public function existsPathInColumn(int $userId, string $column, string $path): bool
    {
        $allowedColumns = [
            'file_cert_disability',
            'file_cert_army',
            'file_cert_professional_credentials'
        ];

        if (!in_array($column, $allowedColumns)) {
            return false;
        }

        return PersonalDataExtra::where('user_id', $userId)
            ->where($column, $path)
            ->exists();
    }
}