<?php

namespace Modules\ProfessionalRecords\Applications;

use Modules\ProfessionalRecords\Repositories\CertificationRepository;

class ListCertificationsCase
{
    public function __construct(private CertificationRepository $repository) {}

    public function exec(int $userId): array
    {
        $records = $this->repository->getByUserId($userId);

        return $records->map(function ($record) {
            return [
                'id' => $record->id,
                'name' => $record->name,
                'year' => $record->year,
                'hours' => $record->hours,
                'file' => $record->file,
                'created_at' => $record->created_at->toISOString(),
                'updated_at' => $record->updated_at->toISOString(),
            ];
        })->toArray();
    }
}
