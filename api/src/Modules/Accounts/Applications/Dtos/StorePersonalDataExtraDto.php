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
        public readonly int $gender,
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

    public function toArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'department_id' => $this->department_id,
            'province_id' => $this->province_id,
            'district_id' => $this->district_id,
            'address' => $this->address,
            'birthday' => $this->birthday,
            'gender' => $this->gender,
            'ruc' => $this->ruc,
            'have_cert_disability' => $this->have_cert_disability,
            'file_cert_disability' => $this->file_cert_disability,
            'have_cert_army' => $this->have_cert_army,
            'file_cert_army' => $this->file_cert_army,
            'have_cert_professional_credentials' => $this->have_cert_professional_credentials,
            'file_cert_professional_credentials' => $this->file_cert_professional_credentials,
            'is_active_cert_professional_credentials' => $this->is_active_cert_professional_credentials,
        ];
    }
}