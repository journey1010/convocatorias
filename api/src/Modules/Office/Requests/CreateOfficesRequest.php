<?php

namespace Modules\Office\Requests;

use Modules\Auth\Shared\Requests\Template;

class CreateOfficesRequest extends Template
{
    public function authorize(): bool
    {
        return $this->verifyPermission('office.manage');
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:offices,name'
            ],
            'locale_id' => [
                'required',
                'integer',
                'exists:locales,id'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nombre es requerido',
            'name.string' => 'Nombre debe ser texto',
            'name.max' => 'Nombre debe tener máximo 255 caracteres',
            'name.unique' => 'Nombre ya existe',
            'locale_id.required' => 'Local es requerido',
            'locale_id.integer' => 'Local debe ser un número entero',
            'locale_id.exists' => 'El local no existe'
        ];
    }
}
