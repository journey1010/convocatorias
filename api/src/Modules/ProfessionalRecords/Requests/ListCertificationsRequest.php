<?php

namespace Modules\ProfessionalRecords\Requests;

use Modules\Auth\Shared\Requests\Template;

class ListCertificationsRequest extends Template
{
    public function authorize(): bool
    {
        return $this->verifyPermission(['p.postulante', 'cv.evaluation']);
    }

    public function rules(): array
    {
        return [];
    }
}
