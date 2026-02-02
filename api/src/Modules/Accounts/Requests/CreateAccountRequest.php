<?php

namespace Modules\Accounts\Requests;

use Modules\Shared\Requests\Template;
use Illuminate\Validation\Rules\Password;

class CreateAccountRequest extends Template {
    
    public function rules(): array
    {
        return [
            'token' => 'required|string',
            'g-recaptcha-response' => 'required|recaptchav3:register,0.5', 
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dni' => 'required|string|regex:/^\d{8}$/|unique:users,dni',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', Password::min(7)->letters()->numbers()],
            'phone' => ['required', 'string', 'regex:/^9\d{8}$/'],
        ];
    }
    
    public function messages()
    {
        return [
            'dni.unique' => 'Este número de DNI ya se encuentra en uso.',
            'nickname.unique' => 'Este nickname ya esta en uso.',
            'email.unique' => 'Este correo ya se encuentra en uso', 
            'password.required' => 'Nueva contraseña requerida.',
            'password.min' => 'La contraseña debe tener mínimo 7 caracteres.',
            'password.numbers' => 'La contraseña debe contener números.',
            'password.letters' => 'La contraseña debe contener letras.',
            'phone.regex' => 'Número de telefono invalido.',
            'g-recaptcha-response.recaptchav3' => 'Error al validar el reCAPTCHA, por favor intente de nuevo.',
        ];
    }
}