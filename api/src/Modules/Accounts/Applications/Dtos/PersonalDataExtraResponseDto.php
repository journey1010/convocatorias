<?php

namespace Modules\Accounts\Applications\Dtos;

use Illuminate\Support\Facades\Storage;

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
        /** @var Cloud $disk */
        $disk = Storage::disk('private');

        return new self(
            department_id: $model->department_id,
            province_id: $model->province_id,
            district_id: $model->district_id,
            address: $model->address,
            birthday: $model->birthday->format('Y-m-d'),
            genere: $model->genere,
            have_cert_disability: $model->have_cert_disability,
            
            file_cert_disability: $model->file_cert_disability 
                ? $disk->url($model->file_cert_disability) 
                : null,
            
            have_cert_army: $model->have_cert_army,
            file_cert_army: $model->file_cert_army 
                ? $disk->url($model->file_cert_army) 
                : null,
            
            have_cert_professional_credentials: $model->have_cert_professional_credentials,
            file_cert_professional_credentials: $model->file_cert_professional_credentials 
                ? $disk->url($model->file_cert_professional_credentials) 
                : null,
            
            is_active_cert_professional_credentials: $model->is_active_cert_professional_credentials,
        );
    }
}