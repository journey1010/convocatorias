<?php

namespace Modules\ProfessionalRecords\Applications\Dtos;

use Modules\ProfessionalRecords\Models\JobRecord;

class JobRecordResponseDto
{
    public function __construct(
        public readonly int $id,
        public readonly int $user_id,
        public readonly string $entity_name,
        public readonly int $type,
        public readonly string $specialization_area,
        public readonly int $status,
        public readonly ?string $description,
        public readonly string $start_date,
        public readonly ?string $end_date,
        public readonly ?string $file,
        public readonly string $created_at,
        public readonly string $updated_at,
    ) {}

    public static function fromModel(JobRecord $record): self
    {
        return new self(
            id: $record->id,
            user_id: $record->user_id,
            entity_name: $record->entity_name,
            type: $record->type,
            specialization_area: $record->specialization_area,
            status: $record->status,
            description: $record->description,
            start_date: $record->start_date->format('Y-m-d'),
            end_date: $record->end_date?->format('Y-m-d'),
            file: $record->file,
            created_at: $record->created_at->toISOString(),
            updated_at: $record->updated_at->toISOString(),
        );
    }
}
