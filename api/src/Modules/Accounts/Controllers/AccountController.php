<?php

namespace Modules\Accounts\Controllers;

use Modules\Accounts\Requests\{
    CreateAccountRequest,
};
use Modules\Accounts\Applications\{
    RegisterAccountUserCase
};

use Symfony\Component\HttpFoundation\JsonResponse;

class AccountController extends \Modules\Shared\Controllers\Controller {
    
    public function register(CreateAccountRequest $request, RegisterAccountUserCase $userCase): JsonResponse
    {   
        $dto = $userCase->exec($request);
        return response()->json($dto, 201);          
    }
}