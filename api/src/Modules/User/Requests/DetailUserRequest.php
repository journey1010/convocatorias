<?php

namespace Modules\User\Requests;

use Modules\Auth\Shared\Requests\Template;

class DetailUserRequest extends Template
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:users,id'
        ];
    }
}
