<?php

namespace Modules\Auth\Requests;

use Modules\Auth\Shared\Requests\Template;

class LoginRequest extends Template {
    public function rules(): array
    {
        return [
            'nickname' => 'required|string',
            'password' => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'nickname.required' => 'Nickname es requerido.',
            'password.required' => 'Password es requerido.', 
        ];
    }
}