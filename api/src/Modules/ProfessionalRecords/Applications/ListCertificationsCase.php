<?php

namespace Modules\ProfessionalRecords\Applications;

use Modules\ProfessionalRecords\Repositories\CertificationRepository;
use Modules\Shared\Services\FileUrlHelper;

class ListCertificationsCase
{
    public function __construct(private CertificationRepository $repository) {}

    public function exec(int $userId)
    {
        $records = $this->repository->getByUserId($userId);
        
        $file = FileUrlHelper::getFileUrl('professional-records.files.download', 'filePath', $records->file);

        return $records->map(function ($record) use ($file) {
            return [
                'id' => $record->id,
                'name' => $record->name,
                'year' => $record->year,
                'hours' => $record->hours,
                'file' => $file
            ];
        });
    }
}