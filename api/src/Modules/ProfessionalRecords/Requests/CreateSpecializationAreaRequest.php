<?php

namespace Modules\ProfessionalRecords\Requests;

use Modules\Auth\Shared\Requests\Template;

class CreateSpecializationAreaRequest extends Template
{
    public function authorize(): bool
    {
        return $this->verifyPermission(['specialization_areas.manage']);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:specialization_areas,name',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es requerido',
            'name.max' => 'El nombre no debe exceder 255 caracteres',
            'name.unique' => 'Ya existe un área de especialización con ese nombre',
        ];
    }
}
