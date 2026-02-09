<?php

namespace Modules\User\Applications;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Infrastructure\Exceptions\JsonResponseException;
use Modules\User\Models\User;
use Modules\User\Services\UserAuth;

class ChangePasswordCase
{
    public function exec(Request $request): void
    {
        $user = User::findOrFail($request->input('user_id'));

        // Verificar que la contraseña actual es correcta
        if (!Hash::check($request->input('current_password'), $user->password)) {
            throw new JsonResponseException('La contraseña actual es incorrecta', 422);
        }

        // Actualizar contraseña
        $user->update([
            'password' => $request->input('new_password')
        ]);

        UserAuth::clearUserCache($user->id);
    }
}
