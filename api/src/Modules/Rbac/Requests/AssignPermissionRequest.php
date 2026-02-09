<?php

namespace Modules\Rbac\Requests;

use Modules\Auth\Shared\Requests\Template;

class AssignPermissionRequest extends Template
{
    public function authorize(): bool
    {
        return $this->verifyPermission('rbac.role');
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
