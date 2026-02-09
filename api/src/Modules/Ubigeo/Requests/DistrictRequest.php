<?php

namespace Modules\Ubigeo\Requests;

use Modules\Auth\Shared\Requests\Template;

class DistrictRequest extends Template
{

    public function rules(): array
    {
        return [
            'province_id' => 'required|integer',
            'name' => 'nullable'
        ];
    }
}
