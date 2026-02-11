<?php

namespace Modules\JobVacancies\Requests;

use Modules\Auth\Shared\Requests\Template;

class UpdateFileNameRequest extends Template
{
    public function authorize(): bool
    {
        // Solo administradores
        return $this->verifyPermission(['p.admin', 'p.rrhh']);
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:job_vacancy_files,id',
            'name' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'El ID del archivo es requerido',
            'id.exists' => 'El archivo no existe',
            'name.required' => 'El nombre es requerido',
            'name.max' => 'El nombre no debe exceder 255 caracteres',
        ];
    }
}
