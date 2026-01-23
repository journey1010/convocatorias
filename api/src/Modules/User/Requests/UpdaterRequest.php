<?php

namespace Modules\User\Requests;

use Modules\Shared\Requests\Template;
use Illuminate\Validation\Rules\Password;

class UpdaterRequest extends Template {
        
    public function authorize(): bool
    {
        return $this->verifyPermission($this->attributes->get('permissions'), 'user.edit');
    }

    public function rules(): array
    {        
        return [
            'id' => ['required', 'integer'],
            'name' => ['nullable', 'string', 'max:255'],
            'last_name' => 'nullable|string|max:255',
            'dni' =>  'nullable|regex:/^\d{8}$/',
            'email' => [
                'nullable', 
                'email',
                'max:255'
            ],
            'roles' => 'nullable|array',
            'roles.*' => 'integer|exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id', 
            'password' => ['nullable', 'string', 'max:255', Password::min(7)->letters()->numbers()],
            'status' => ['nullable', 'boolean'],
            'phone' => ['nullable', 'string', 'regex:/^9\d{8}$/'],
            'office_id' => ['nullable', 'integer']
        ];
    }

    public function messages(): array
    {
        return [
            'password.min' => 'La contraseña requiere al menos 7 caracteres.',
            'password.letters' => 'La contraseña requiere al menos una letra.',
            'password.numbers' => 'La contraseña requiere al menos un número.',
            'password.symbols' => 'La contraseña requiere al menos un símbolo.',
            'email.email' => 'El correo electrónico no es válido.',
        ];
    }
}