<?php

namespace Modules\ProfessionalRecords\Applications\Dtos;

use Modules\ProfessionalRecords\Models\AcademicRecord;
use Modules\Shared\Services\FileUrlHelper;

class AcademicRecordResponseDto
{
    public function __construct(
        public readonly int $id,
        public readonly int $user_id,
        public readonly ?int $specialization_area_id,
        public readonly string $specialization_area_name,
        public readonly int $level,
        public readonly int $status,
        public readonly string $start_date,
        public readonly ?string $end_date,
        public readonly ?string $description,
        public readonly string $file
    ) {}

    public static function fromModel(AcademicRecord $record): self
    {
        $fileUrl = FileUrlHelper::getFileUrl('professional-records.files.download', 'filePath', $record->file);

        return new self(
            id: $record->id,
            user_id: $record->user_id,
            specialization_area_id: $record->specialization_area_id,
            specialization_area_name: $record->specializationArea?->name,
            level: $record->level,
            status: $record->status,
            start_date: $record->start_date,
            end_date: $record->end_date,
            description: $record->description,
            file: $fileUrl
        );
    }
}