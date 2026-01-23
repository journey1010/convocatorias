<?php

namespace Modules\Rbac\Requests;

use Modules\Shared\Requests\Template;

class ListerRbacRequest extends Template{
    
    public function authorize(): bool
    {
        return $this->verifyPermission($this->attributes->get('permissions'), ['rbac.role']);
    }

    public function rules(): array
    {
        return [
            'page' => 'nullable|integer|min:1',
            'itemsPerPage' => 'nullable|required_with:page|integer|min:1|max:100',
        ];
    }
}