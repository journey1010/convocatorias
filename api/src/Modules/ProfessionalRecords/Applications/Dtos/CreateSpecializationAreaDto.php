<?php

namespace Modules\ProfessionalRecords\Applications\Dtos;

class CreateSpecializationAreaDto
{
    public function __construct(
        public readonly string $name,
    ) {}
}
