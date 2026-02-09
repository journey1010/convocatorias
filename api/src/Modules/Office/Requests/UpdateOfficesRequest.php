<?php

namespace Modules\Office\Requests;

use Modules\Auth\Shared\Requests\Template;

class UpdateOfficesRequest extends Template
{
    public function authorize(): bool
    {
        return $this->verifyPermission('offices.manage');
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer',
            'name' => 'required|string',
            'status' => 'required|boolean',
            'locale_id' => 'required|integer|exists:locales,id',
        ];
    }

    public function message(): array
    {
        return [
            'id.required' => 'El id es requerido',
            'id.integer' => 'El id debe ser un número entero',
            'name.required' => 'El nombre es requerido',
            'name.string' => 'El nombre debe ser una cadena de texto',
            'name.unique' => 'El nombre ya existe',
            'status.required' => 'El estado es requerido',
            'status.boolean' => 'El estado debe ser un valor booleano',
            'locale_id.required' => 'El local es requerido',
            'locale_id.integer' => 'El local debe ser un número entero',
            'locale_id.exists' => 'El local seleccionado no existe',
        ];
    }
}
