<?php

namespace Modules\Ubigeo\Requests;

use Modules\Shared\Requests\Template;

class DepartmentRequest extends Template{

    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255'
        ];
    }
}