<?php

namespace Modules\ProfessionalRecords\Applications\Dtos;

use Modules\ProfessionalRecords\Models\SpecializationArea;

class SpecializationAreaResponseDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $name
    ) {}

    public static function fromModel(SpecializationArea $area): self
    {
        return new self(
            id: $area->id,
            name: $area->name
        );
    }
}
