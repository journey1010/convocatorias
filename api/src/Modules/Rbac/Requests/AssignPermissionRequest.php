<?php

namespace Modules\Rbac\Requests;

use Modules\Shared\Requests\Template;

class AssignPermissionRequest extends Template {
    public function authorize(): bool
    {
        return $this->verifyPermission($this->attributes->get('permissions'), ['rbac.role']);
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer',
            'permission' => 'required|array', 
            'permission.*' => 'integer|exists:permissions,id'
        ];
    }
}