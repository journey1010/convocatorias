<?php

namespace Modules\JobVacancies\Applications\Dtos;

class CreateJobVacancyDto
{
    public function __construct(
        public readonly int $created_by,
        public readonly int $locale_id,
        public readonly string $title,
        public readonly int $status,
        public readonly bool $mode,
        public readonly string $start_date,
        public readonly string $close_date,
        public readonly array $profiles = [], // Array of profile data
    ) {}
}