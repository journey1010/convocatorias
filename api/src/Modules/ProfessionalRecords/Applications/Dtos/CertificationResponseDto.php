<?php

namespace Modules\ProfessionalRecords\Applications\Dtos;

use Modules\ProfessionalRecords\Models\Certification;
use Modules\Shared\Services\FileUrlHelper;

class CertificationResponseDto
{
    public function __construct(
        public readonly int $id,
        public readonly int $user_id,
        public readonly string $name,
        public readonly int $year,
        public readonly int $hours,
        public readonly string $file
    ) {}

    public static function fromModel(Certification $record): self
    {
        $file = FileUrlHelper::getFileUrl('professional-records.files.download', 'filePath', $record->file);
        return new self(
            id: $record->id,
            user_id: $record->user_id,
            name: $record->name,
            year: $record->year,
            hours: $record->hours,
            file: $file,
        );
    }
}