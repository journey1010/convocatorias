<?php

namespace Modules\ProfessionalRecords\Applications\Dtos;

use Illuminate\Http\UploadedFile;

class CreateCertificationDto
{
    public function __construct(
        public readonly int $user_id,
        public readonly string $name,
        public readonly int $year,
        public readonly int $hours,
        public readonly UploadedFile $file,
    ) {}
}
