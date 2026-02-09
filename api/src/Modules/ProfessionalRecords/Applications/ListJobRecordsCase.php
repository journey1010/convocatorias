<?php

namespace Modules\ProfessionalRecords\Applications;

use Modules\ProfessionalRecords\Repositories\JobRecordRepository;

class ListJobRecordsCase
{
    public function __construct(private JobRecordRepository $repository) {}

    public function exec(int $userId): array
    {
        $records = $this->repository->getByUserId($userId);

        return $records->map(function ($record) {
            return [
                'id' => $record->id,
                'entity_name' => $record->entity_name,
                'type' => $record->type,
                'specialization_area' => $record->specialization_area,
                'status' => $record->status,
                'description' => $record->description,
                'start_date' => $record->start_date->format('Y-m-d'),
                'end_date' => $record->end_date?->format('Y-m-d'),
                'file' => $record->file,
                'created_at' => $record->created_at->toISOString(),
                'updated_at' => $record->updated_at->toISOString(),
            ];
        })->toArray();
    }
}
