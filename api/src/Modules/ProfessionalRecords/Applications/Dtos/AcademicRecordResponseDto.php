<?php

namespace Modules\ProfessionalRecords\Applications\Dtos;

use Modules\ProfessionalRecords\Models\AcademicRecord;

class AcademicRecordResponseDto
{
    public function __construct(
        public readonly int $id,
        public readonly int $user_id,
        public readonly int $specialization_area_id,
        public readonly string $specialization_area_name,
        public readonly int $level,
        public readonly int $status,
        public readonly string $start_date,
        public readonly ?string $end_date,
        public readonly ?string $description,
        public readonly string $file,
        public readonly string $created_at,
        public readonly string $updated_at,
    ) {}

    public static function fromModel(AcademicRecord $record): self
    {
        return new self(
            id: $record->id,
            user_id: $record->user_id,
            specialization_area_id: $record->specialization_area_id,
            specialization_area_name: $record->specializationArea->name,
            level: $record->level,
            status: $record->status,
            start_date: $record->start_date->format('Y-m-d'),
            end_date: $record->end_date?->format('Y-m-d'),
            description: $record->description,
            file: $record->file,
            created_at: $record->created_at->toISOString(),
            updated_at: $record->updated_at->toISOString(),
        );
    }
}
