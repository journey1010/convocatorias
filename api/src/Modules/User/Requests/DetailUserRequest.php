<?php

namespace Modules\User\Requests;

use Modules\Shared\Requests\Template;

class DetailUserRequest extends Template {
    
    public function verifyPermission(string $stringPermission, string|array $permission, bool $strict = false): bool
    {
        return $this->verifyPermission($this->attributes->get('permissions'), 'user.list');
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:users,id' 
        ];
    }
}