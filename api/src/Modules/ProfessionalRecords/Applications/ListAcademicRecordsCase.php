<?php

namespace Modules\ProfessionalRecords\Applications;

use Modules\ProfessionalRecords\Repositories\AcademicRecordRepository;
use Modules\Shared\Services\FileUrlHelper;

class ListAcademicRecordsCase
{
    public function __construct(private AcademicRecordRepository $repository) {}

    public function exec(int $userId)
    {
        $records = $this->repository->getByUserId($userId);

        return $records->map(function ($record) {
            $file = FileUrlHelper::getFileUrl('professional-records.files.download', 'filePath', $record->file);
            return [
                'id' => $record->id,
                'specialization_area_id' => $record->specialization_area_id,
                'specialization_area_name' => $record->specializationArea->name,
                'level' => $record->level,
                'status' => $record->status,
                'start_date' => $record->start_date,
                'end_date' => $record->end_date,
                'description' => $record->description,
                'file' => $file,
            ];
        });
    }
}