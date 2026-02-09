<?php

namespace Modules\ProfessionalRecords\Requests;

use Modules\Auth\Shared\Requests\Template;

class DeleteAcademicRecordRequest extends Template
{
    public function authorize(): bool
    {
        return $this->verifyPermission(['p.postulante']);
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:academic_records,id',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'El ID del registro es requerido',
            'id.exists' => 'El registro académico no existe',
        ];
    }
}
