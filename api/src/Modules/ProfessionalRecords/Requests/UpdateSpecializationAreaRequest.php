<?php

namespace Modules\ProfessionalRecords\Requests;
use Illuminate\Validation\Rule;

use Modules\Auth\Shared\Requests\Template;

class UpdateSpecializationAreaRequest extends Template
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return $this->verifyPermission(['specialization_areas.manage']);
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer',
            'name' => [
                'required',
                'string',
                Rule::unique('specialization_areas', 'name')->ignore($this->id)
            ],
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