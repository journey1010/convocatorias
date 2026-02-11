<?php

namespace Modules\JobVacancies\Applications\Dtos;

use Illuminate\Http\UploadedFile;

class JobVacancyFileDto
{
    public function __construct(
        public readonly ?int $id, // For updates
        public readonly int $job_vacancy_id,
        public readonly ?UploadedFile $file, // Null when just updating name
        public readonly string $name, // Display name
    ) {}
}
