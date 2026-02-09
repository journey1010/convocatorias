<?php

namespace Modules\ProfessionalRecords\Applications\Dtos;

use Modules\ProfessionalRecords\Models\Certification;

class CertificationResponseDto
{
    public function __construct(
        public readonly int $id,
        public readonly int $user_id,
        public readonly string $name,
        public readonly int $year,
        public readonly int $hours,
        public readonly string $file,
        public readonly string $created_at,
        public readonly string $updated_at,
    ) {}

    public static function fromModel(Certification $record): self
    {
        return new self(
            id: $record->id,
            user_id: $record->user_id,
            name: $record->name,
            year: $record->year,
            hours: $record->hours,
            file: $record->file,
            created_at: $record->created_at->toISOString(),
            updated_at: $record->updated_at->toISOString(),
        );
    }
}
