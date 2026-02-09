<?php

namespace Modules\User\Requests;

use Modules\Auth\Shared\Requests\Template;
use Illuminate\Validation\Rules\Password;

class ChangePasswordRequest extends Template
{
    public function authorize(): bool
    {
        return true; // Usuarios pueden cambiar su propia contraseña
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'current_password' => 'required|string',
            'new_password' => ['required', Password::min(7)->letters()->numbers()],
            'new_password_confirmation' => 'required|same:new_password',
        ];
    }

    public function messages(): array
    {
        return [
            'new_password.min' => 'La contraseña requiere al menos 7 caracteres.',
            'new_password.letters' => 'La contraseña requiere al menos una letra.',
            'new_password.numbers' => 'La contraseña requiere al menos un número.',
            'new_password_confirmation.same' => 'Las contraseñas no coinciden.',
        ];
    }
}
