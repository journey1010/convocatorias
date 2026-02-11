<?php

namespace Modules\Accounts\Applications\Dtos;

use Illuminate\Http\UploadedFile;

class StorePersonalDataExtraDto
{
    public function __construct(
        public readonly int $user_id,
        public readonly int $department_id,
        public readonly int $province_id,
        public readonly int $district_id,
        public readonly string $address,
        public readonly string $birthday,
        public readonly int $genere,
        public readonly string $ruc,
        public readonly ?UploadedFile $file_dni,
        public readonly bool $have_cert_disability,
        public readonly ?UploadedFile $file_cert_disability,
        public readonly bool $have_cert_army,
        public readonly ?UploadedFile $file_cert_army,
        public readonly bool $have_cert_professional_credentials,
        public readonly ?UploadedFile $file_cert_professional_credentials,
        public readonly bool $is_active_cert_professional_credentials,
    ) {}
}
