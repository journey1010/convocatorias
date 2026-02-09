<?php

namespace Modules\ProfessionalRecords\Applications\Dtos;

use Illuminate\Http\UploadedFile;

class CreateAcademicRecordDto
{
    public function __construct(
        public readonly int $user_id,
        public readonly int $specialization_area_id,
        public readonly int $level,
        public readonly int $status,
        public readonly string $start_date,
        public readonly ?string $end_date,
        public readonly ?string $description,
        public readonly UploadedFile $file,
    ) {}
}
