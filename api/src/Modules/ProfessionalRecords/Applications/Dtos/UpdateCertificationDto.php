<?php

namespace Modules\ProfessionalRecords\Applications\Dtos;

use Illuminate\Http\UploadedFile;

class UpdateCertificationDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly int $year,
        public readonly int $hours,
        public readonly ?UploadedFile $file,
    ) {}
}
