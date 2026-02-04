<?php

namespace Modules\Accounts\Applications;

use Illuminate\Http\Request;
use Modules\Auth\Services\Tokens\{
    JwtManager,
    Exceptions\TokenException,
    Enum\TokenType,
};
use Modules\User\{
    Models\User,
    Enums\TypeUser,
    Enums\StatusUser
};
use Infrastructure\Exceptions\JsonResponseException;
use Modules\Accounts\Applications\Dtos\RegisterAccountDto;
use Illuminate\Support\Facades\{ DB, Cache};

class RegisterAccountUserCase {
   
    public function __construct(protected JwtManager $jwtManager) {}

    public function exec(Request $request): RegisterAccountDto
    {
        $inputs = $request->all();
        $token = $inputs['token'];
        $fingerprint = $request->header('X-Fingerprint');

        $this->validateTokenState($token, $fingerprint);

        $this->registerUser($inputs);

        Cache::forget("register_token:{$fingerprint}");
        
        return new RegisterAccountDto(
            email: $inputs['email'],
            password: $inputs['password']
        );
    }

    private function validateTokenState(string $token, ?string $fingerprint): void
    {
        if (!$fingerprint) {
            throw new JsonResponseException('Identificador de dispositivo no encontrado', 400);
        }

        $claims = $this->checkJwtIntegrity($token);

        if (($claims->type_client ?? '') !== TokenType::FOR_REGISTER->value) {
            throw new JsonResponseException('El propósito del token es inválido', 403);
        }

        $storedToken = Cache::get("register_token:{$fingerprint}");

        if (!$storedToken) {
            throw new JsonResponseException('El tiempo de registro ha expirado o el token ya fue usado', 403);
        }

        if ($storedToken !== $token) {
            throw new JsonResponseException('Se ha generado un nuevo token. Por favor, use el más reciente.', 403);
        }
    }

    private function checkJwtIntegrity(string $token): object
    {
        try {
            return $this->jwtManager->decode($token);
        } catch (TokenException $e) {
            $msg = $e->getMessage() === 'Token has expired' 
                ? 'El enlace de registro ha caducado' 
                : 'La firma de seguridad es inválida';
                
            throw new JsonResponseException($msg, 403);
        }
    }

    private function registerUser(array $inputs): void
    {
        DB::transaction(function () use ($inputs) {
            $user = User::create([
                'name'      => $inputs['name'], 
                'last_name' => $inputs['last_name'],
                'dni'       => $inputs['dni'],
                'nickname'  => $inputs['email'],
                'email'     => $inputs['email'],
                'phone'     => $inputs['phone'],
                'password'  => $inputs['password'],
                'status'    => StatusUser::INACTIVE->value,
                'level'     => 1,
                'type_user' => TypeUser::citizen->value,
            ]);

            $user->syncPermissions(['postulante']);
        });
    }
}