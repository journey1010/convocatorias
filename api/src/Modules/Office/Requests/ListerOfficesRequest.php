<?php

namespace Modules\Office\Requests;

use Modules\Auth\Shared\Requests\Template;

class ListerOfficesRequest extends Template
{
    public function authorize(): bool
    {
        return $this->verifyPermission('offices.manage');
    }


    public function rules(): array
    {
        return [
            'itemsPerPage' => ['nullable', 'required_with:page', 'integer'],
            'page' => ['nullable', 'required_with:itemsPerPage', 'integer'],
            'name' => 'nullable|string|max:255',
            'status' => 'nullable|boolean',
        ];
    }
}
