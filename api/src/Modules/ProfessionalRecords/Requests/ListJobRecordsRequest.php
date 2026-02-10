<?php

namespace Modules\ProfessionalRecords\Requests;

use Modules\Auth\Shared\Requests\Template;

class ListJobRecordsRequest extends Template
{
    public function authorize(): bool
    {
        return $this->verifyPermission('p.postulante');
    }

    public function rules(): array
    {
        return [];
    }
}