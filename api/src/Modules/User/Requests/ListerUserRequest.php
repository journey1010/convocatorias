<?php

namespace Modules\User\Requests;

use Modules\Auth\Shared\Requests\Template;

class ListerUserRequest extends Template
{
    public function authorize(): bool
    {
        return $this->verifyPermission('user.list');
    }

    public function rules(): array
    {
        return [
            'page' => 'required|integer|min:1',
            'itemsPerPage' => 'required|integer|min:1|max:100',
            'search' => 'nullable|string|max:255',
            'office_id' => 'nullable|integer|exists:offices,id'
        ];
    }

    public function messages(): array
    {
        return [
            'itemsPerPage.max' => 'Máximo 100 elementos por página.',
        ];
    }
}
