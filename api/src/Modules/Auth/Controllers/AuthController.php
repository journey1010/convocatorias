<?php

namespace Modules\Auth\Controllers;

use Illuminate\Http\Request;
use Modules\Auth\Requests\LoginRequest;
use Modules\Auth\Applications\{LoginUserCase, RefreshUserCase};
use Modules\Auth\Infrastructure\Context\RequestContextResolver;

class AuthController extends \Modules\Shared\Controllers\Controller
{
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
        $refreshData = RequestContextResolver::getRefreshData($request);

        $dto = $login->exec(
            $refreshData['userId'],
            $refreshData['exp'],
            $request?->bearerToken()
        );

        return response()->json($dto);
    }
}
