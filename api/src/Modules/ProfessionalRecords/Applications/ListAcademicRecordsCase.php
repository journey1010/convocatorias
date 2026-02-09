<?php

namespace Modules\ProfessionalRecords\Applications;

use Modules\ProfessionalRecords\Repositories\AcademicRecordRepository;

class ListAcademicRecordsCase
{
    public function __construct(private AcademicRecordRepository $repository) {}

    public function exec(int $userId): array
    {
        $records = $this->repository->getByUserId($userId);

        return $records->map(function ($record) {
            return [
                'id' => $record->id,
                'specialization_area_id' => $record->specialization_area_id,
                'specialization_area_name' => $record->specializationArea->name,
                'level' => $record->level,
                'status' => $record->status,
                'start_date' => $record->start_date->format('Y-m-d'),
                'end_date' => $record->end_date?->format('Y-m-d'),
                'description' => $record->description,
                'file' => $record->file,
                'created_at' => $record->created_at->toISOString(),
                'updated_at' => $record->updated_at->toISOString(),
            ];
        })->toArray();
    }
}
