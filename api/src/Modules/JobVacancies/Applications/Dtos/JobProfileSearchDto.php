<?php

namespace Modules\JobVacancies\Applications\Dtos;

use Modules\JobVacancies\Enums\VacancyStatus;

class JobProfileSearchDto
{
    public function __construct(
        public readonly int $page,
        public readonly int $per_page,
        public readonly ?int $locale_id,
        public readonly ?int $office_id,
        public readonly ?int $specialization_area_id,
        public readonly ?VacancyStatus $status,
        public readonly ?float $salary,
        public readonly ?string $title,
        public readonly ?string $code_profile
    ) {}
}