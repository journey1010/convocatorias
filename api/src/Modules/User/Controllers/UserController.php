<?php

namespace Modules\User\Controllers;

use Modules\User\Requests\{
    StoreUserRequest, 
    ListerUserRequest,
    UpdaterRequest, 
    DetailUserRequest
};

use Modules\User\Applications\{
    UpdateUserCase,
    StoreUserCase ,
    ListUserCase
};

class UserController
{
    public function register(StoreUserRequest $request, StoreUserCase $user)
    {
        $user->exec($request);
        return response()->json(['message' => 'Usuario creado']);
    }
    
    public function Lister(ListerUserRequest $request, ListUserCase $user)
    { 
        $results = $user->exec($request);
        return response()->json($results);
    }

    public function lister()
    {
    
    }

    public function updater(UpdaterRequest $request, UpdateUserCase $user)
    {
        $user->exec($request);
        return response()->json(['message' => 'Datos actualizados']);
    }
}