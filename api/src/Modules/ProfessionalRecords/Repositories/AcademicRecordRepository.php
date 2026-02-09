<?php

namespace Modules\ProfessionalRecords\Repositories;

use Modules\ProfessionalRecords\Models\AcademicRecord;
use Infrastructure\Exceptions\JsonResponseException;

class AcademicRecordRepository
{
    private const MAX_RECORDS_PER_USER = 40;

    public function findById(int $id): ?AcademicRecord
    {
        return AcademicRecord::with('specializationArea')->find($id);
    }

    public function findByIdOrFail(int $id): AcademicRecord
    {
        $record = $this->findById($id);

        if (!$record) {
            throw new JsonResponseException('Registro académico no encontrado', 404);
        }

        return $record;
    }

    public function getByUserId(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return AcademicRecord::with('specializationArea')
            ->where('user_id', $userId)
            ->orderBy('start_date', 'desc')
            ->get();
    }

    public function countByUserId(int $userId): int
    {
        return AcademicRecord::where('user_id', $userId)->count();
    }

    public function create(array $data): AcademicRecord
    {
        // Check 40-record limit
        if ($this->countByUserId($data['user_id']) >= self::MAX_RECORDS_PER_USER) {
            throw new JsonResponseException(
                'Has alcanzado el límite máximo de ' . self::MAX_RECORDS_PER_USER . ' registros académicos',
                422
            );
        }

        return AcademicRecord::create($data);
    }

    public function update(AcademicRecord $record, array $data): AcademicRecord
    {
        $record->update($data);
        return $record->fresh(['specializationArea']);
    }

    public function delete(AcademicRecord $record): bool
    {
        return $record->delete();
    }
}
