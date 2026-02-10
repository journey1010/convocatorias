<?php

namespace Modules\ProfessionalRecords\Applications;

use Modules\ProfessionalRecords\Repositories\JobRecordRepository;

class ListJobRecordsCase
{
    public function __construct(private JobRecordRepository $repository) {}

    public function exec(int $userId)
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
                'start_date' => $record->start_date,
                'end_date' => $record->end_date,
                'file' => $record->file,
            ];
        });
    }
}
