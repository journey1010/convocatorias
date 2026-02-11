<?php

namespace Modules\JobVacancies\Applications\Dtos;

use Illuminate\Http\UploadedFile;

class JobProfileDto
{
    public function __construct(
        public readonly ?int $id, // For updates
        public readonly string $title,
        public readonly string $salary,
        public readonly int $office_id,
        public readonly ?string $code_profile,
        public readonly ?UploadedFile $file, // Profile description file
    ) {}
}
