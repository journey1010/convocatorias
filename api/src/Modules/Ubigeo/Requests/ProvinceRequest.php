<?php

namespace Modules\Ubigeo\Requests;

use Modules\Auth\Shared\Requests\Template;

class ProvinceRequest extends Template
{

    public function rules(): array
    {
        return [
            'department_id' => 'required|integer',
            'name' => 'nullable|string|max:255'
        ];
    }
}
