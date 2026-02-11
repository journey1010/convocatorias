<?php

namespace Modules\JobVacancies\Applications\Dtos;

class UpdateJobVacancyDto
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $title = null,
        public readonly ?int $status = null,
        public readonly ?bool $mode = null,
        public readonly ?string $start_date = null,
        public readonly ?string $close_date = null,
    ) {}
}
