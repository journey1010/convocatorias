<?php

namespace Modules\ProfessionalRecords\Requests;

use Modules\Auth\Shared\Requests\Template;

class DeleteCertificationRequest extends Template
{
    public function authorize(): bool
    {
        return $this->verifyPermission(['p.postulante']);
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer',
        ];
    }
}