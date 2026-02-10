<?php

namespace Modules\ProfessionalRecords\Repositories;

use Modules\ProfessionalRecords\Models\Certification;
use Infrastructure\Exceptions\JsonResponseException;

class CertificationRepository
{
    private const MAX_RECORDS_PER_USER = 40;

    public function findById(int $id): ?Certification
    {
        return Certification::find($id);
    }

    public function findByIdOrFail(int $id): Certification
    {
        $record = $this->findById($id);

        if (!$record) {
            throw new JsonResponseException('Certificación no encontrada', 404);
        }

        return $record;
    }

    public function getByUserId(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return Certification::where('user_id', $userId)
            ->orderBy('year', 'desc')
            ->get();
    }

    public function countByUserId(int $userId): int
    {
        return Certification::where('user_id', $userId)->count();
    }

    public function create(array $data): Certification
    {
        // Check 40-record limit
        if ($this->countByUserId($data['user_id']) >= self::MAX_RECORDS_PER_USER) {
            throw new JsonResponseException(
                'Has alcanzado el límite máximo de ' . self::MAX_RECORDS_PER_USER . ' certificaciones',
                422
            );
        }

        return Certification::create($data);
    }

    public function update(Certification $record, array $data): Certification
    {
        $record->update($data);
        return $record;
    }

    public function delete(Certification $record): bool
    {
        return $record->delete();
    }
}
