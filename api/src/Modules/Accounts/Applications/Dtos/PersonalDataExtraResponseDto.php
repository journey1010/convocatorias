<?php

namespace Modules\Accounts\Applications\Dtos;

use Modules\Shared\Services\FileUrlHelper;

class PersonalDataExtraResponseDto
{
    public function __construct(
        public readonly int $department_id,
        public readonly int $province_id,
        public readonly int $district_id,
        public readonly string $address,
        public readonly string $birthday,
        public readonly int $genere,
        public readonly bool $have_cert_disability,
        public readonly ?string $file_cert_disability,
        public readonly bool $have_cert_army,
        public readonly ?string $file_cert_army,
        public readonly bool $have_cert_professional_credentials,
        public readonly ?string $file_cert_professional_credentials,
        public readonly bool $is_active_cert_professional_credentials,
    ) {}

    public static function fromModel($model): self
    {
        $endpoint = '/api/accounts/personal-data/certificate';

        return new self(
            department_id: $model->department_id,
            province_id: $model->province_id,
            district_id: $model->district_id,
            address: $model->address,
            birthday: $model->birthday->format('Y-m-d'),
            genere: $model->genere,
            have_cert_disability: $model->have_cert_disability,
            file_cert_disability:FileUrlHelper::getFileUrl($endpoint, $model->file_cert_disability),       
            have_cert_army: $model->have_cert_army,
            file_cert_army: FileUrlHelper::getFileUrl($endpoint, $model->file_cert_army),
            have_cert_professional_credentials: $model->have_cert_professional_credentials,
            file_cert_professional_credentials: FileUrlHelper::getFileUrl($endpoint, $model->file_cert_professional_credentials),   
            is_active_cert_professional_credentials: $model->is_active_cert_professional_credentials,
        );
    }
}