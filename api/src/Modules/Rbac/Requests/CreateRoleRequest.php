<?php

namespace Modules\Rbac\Requests;

use Modules\Shared\Requests\Template;

class CreateRoleRequest extends Template {
    public function authorize(): bool
    {
        return $this->verifyPermission($this->attributes->get('permissions'), ['rbac.role']);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:roles,name', 
            'display_name' => 'required|string|max:255',
            'description' => 'required|string|max:255'
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => 'Este nombre ya existe.'
        ];
    }
}