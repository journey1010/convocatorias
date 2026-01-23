<?php

namespace Modules\Auth\Controllers;

use Illuminate\Http\Request;
use Modules\Auth\Requests\LoginRequest;
use Modules\Auth\Applications\{LoginUserCase, RefreshUserCase};

class AuthController extends \Modules\Shared\Controllers\Controller {
    
    public function login(LoginRequest $request, LoginUserCase $login)
    {
        $dto = $login->exec(
            $request->input('nickname'), 
            $request->input('password')
        );
        return response()->json($dto);
    }

    public function refresh(Request $request, RefreshUserCase $login)
    {
        $dto = $login->exec(
            $request->attributes->get('sub'),
            $request->attributes->get('exp'),
            $request?->bearerToken()
        );

        return response()->json($dto);
    }
}