<?php

namespace Modules\ProfessionalRecords\Applications\Dtos;

use Illuminate\Http\UploadedFile;

class UpdateJobRecordDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $entity_name,
        public readonly int $type,
        public readonly string $specialization_area,
        public readonly int $status,
        public readonly ?string $description,
        public readonly string $start_date,
        public readonly ?string $end_date,
        public readonly ?UploadedFile $file,
    ) {}
}
