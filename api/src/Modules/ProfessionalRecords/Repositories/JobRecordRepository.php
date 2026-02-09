<?php

namespace Modules\ProfessionalRecords\Repositories;

use Modules\ProfessionalRecords\Models\JobRecord;
use Infrastructure\Exceptions\JsonResponseException;

class JobRecordRepository
{
    private const MAX_RECORDS_PER_USER = 40;

    public function findById(int $id): ?JobRecord
    {
        return JobRecord::find($id);
    }

    public function findByIdOrFail(int $id): JobRecord
    {
        $record = $this->findById($id);

        if (!$record) {
            throw new JsonResponseException('Registro laboral no encontrado', 404);
        }

        return $record;
    }

    public function getByUserId(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return JobRecord::where('user_id', $userId)
            ->orderBy('start_date', 'desc')
            ->get();
    }

    public function countByUserId(int $userId): int
    {
        return JobRecord::where('user_id', $userId)->count();
    }

    public function create(array $data): JobRecord
    {
        // Check 40-record limit
        if ($this->countByUserId($data['user_id']) >= self::MAX_RECORDS_PER_USER) {
            throw new JsonResponseException(
                'Has alcanzado el límite máximo de ' . self::MAX_RECORDS_PER_USER . ' registros laborales',
                422
            );
        }

        return JobRecord::create($data);
    }

    public function update(JobRecord $record, array $data): JobRecord
    {
        $record->update($data);
        return $record->fresh();
    }

    public function delete(JobRecord $record): bool
    {
        return $record->delete();
    }
}
