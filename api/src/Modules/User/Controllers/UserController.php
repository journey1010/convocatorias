<?php

namespace Modules\User\Controllers;

use Modules\User\Requests\{
    StoreUserRequest, 
    ListerUserRequest,
    UpdaterRequest, 
    ChangePasswordRequest
};

use Modules\User\Applications\{
    UpdateUserCase,
    StoreUserCase ,
    ListUserCase,
    ChangePasswordCase
};

class UserController
{
    public function register(StoreUserRequest $request, StoreUserCase $case)
    {
        $case->exec($request);
        return response()->json(['message' => 'Usuario creado']);
    }
    
    public function list(ListerUserRequest $request, ListUserCase $case)
    { 
        $results = $case->exec($request);
        return response()->json($results);
    }

    public function update(UpdaterRequest $request, UpdateUserCase $case)
    {
        $case->exec($request);
        return response()->json(['message' => 'Datos actualizados']);
    }

    public function changePassword(ChangePasswordRequest $request, ChangePasswordCase $case)
    {
        $case->exec($request);
        return response()->json(['message' => 'Contraseña actualizada']);
    }
}