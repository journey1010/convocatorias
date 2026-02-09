<?php

namespace Modules\Office\Requests;

use Modules\Auth\Shared\Requests\Template;

class UpdateLocalesRequest extends Template
{
    public function authorize(): bool
    {
        return $this->verifyPermission('offices.manage');
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:locales,id',
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:locales,name,' . $this->input('id')
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'El id es requerido',
            'id.integer' => 'El id debe ser un número entero',
            'id.exists' => 'El local no existe',
            'name.required' => 'Nombre es requerido',
            'name.string' => 'Nombre debe ser texto',
            'name.max' => 'Nombre debe tener máximo 255 caracteres',
            'name.unique' => 'Nombre ya existe'
        ];
    }
}
