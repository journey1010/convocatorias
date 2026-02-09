<?php

namespace Modules\User\Requests;

use Modules\Auth\Shared\Requests\Template;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends Template
{

    public function authorize(): bool
    {
        return $this->verifyPermission('user.create');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dni' => 'required|string|regex:/^\d{8}$/|unique:users,dni',
            'email' => 'required|email|unique:users,email',
            'nickname' => 'required|unique:users,nickname',
            'password' => ['required', Password::min(7)->letters()->numbers()],
            'phone' => ['nullable', 'string', 'regex:/^9\d{8}$/'],
            'office_id' => 'required|integer|exists:offices,id',
            'roles' => 'nullable|array|required_without:direct_permissions',
            'roles.*' => 'integer|exists:roles,id',
            'permissions' => 'nullable|array|required_without:roles',
            'permissions.*' => 'integer|exists:permissions,id',
        ];
    }

    public function messages()
    {
        return [
            'dni.unique' => 'Este número de DNI ya se encuentra en uso.',
            'nickname.unique' => 'Este nickname ya esta en uso.',
            'email.unique' => 'Este correo ya se encuentra en uso',
            'role.in' => 'Rol no disponible para el registro.',
            'role.required' => 'Rol requerido.',
            'role.string' => 'Rol debe ser un string.',
            'role.max' => 'Rol debe tener máximo 255 caracteres.',
            'role.exists' => 'Rol no existe.',
            'password.required' => 'Nueva contraseña requerida.',
            'password.min' => 'La contraseña debe tener mínimo 7 caracteres.',
            'password.numbers' => 'La contraseña debe contener números.',
            'password.letters' => 'La contraseña debe contener letras.',
            'phone.regex' => 'Número de telefono invalido.',
            'officeId.exists' => 'Código de oficina no existe'
        ];
    }
}
