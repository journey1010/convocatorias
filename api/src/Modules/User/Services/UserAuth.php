<?php 

namespace Modules\User\Services;

use Modules\User\Models\OfficeUser;
use Modules\User\Models\Dtos\UserAuthDTO;
use Illuminate\Support\Facades\{Cache, Hash};
use Infrastructure\Exceptions\JsonResponseException;
use Modules\User\Enums\StatusUser;
use Modules\User\Models\QueryServices\VerifyCredentialQueryService;

class UserAuth extends VerifyCredentialQueryService {
    
    private const CACHE_TTL = 2678400;
    private const CACHE_PREFIX = 'user_auth_';

    public function verifyCredentials(string $nickname, string $password): UserAuthDTO
    {
        $user = $this->searchByNickname($nickname);
        
        if (!$user || !Hash::check($password, $user->password)) {
            throw new JsonResponseException('Credenciales incorrectas', 401);
        }

        $this->validateUserStatus($user->status);

        $permissions = $this->getUserPermissions($user->id);
        
        $offices = $user->  $this->getOfficeUser($user->id);
        
        $userDto = new UserAuthDTO(
            id: $user->id,
            name: $user->name,
            last_name: $user->last_name,
            dni: $user->dni, 
            nickname: $user->nickname,
            phone: $user->phone,
            email: $user->email,
            level: $user->level, 
            offices: $offices,
            permissions: $permissions
        );

        Cache::put(
            key: self::CACHE_PREFIX . $user->id, 
            value: $userDto, 
            ttl: self::CACHE_TTL
        );
        
        return $userDto;
    }

    public function refreshUser(int $user_id)
    {
        $user = $this->getCachedUser($user_id);
        
        if($user){
            return $user;
        }
        
        $user = $this->searchByNickname($user_id);

        $this->validateUserStatus($user->status);

        $permissions = $this->getUserPermissions($user->id);
        
        $offices = $this->getOfficeUser($user->id);
        
        $userDto = new UserAuthDTO(
            id: $user->id,
            name: $user->name,
            last_name: $user->last_name,
            dni: $user->dni, 
            nickname: $user->nickname,
            phone: $user->phone,
            email: $user->email,
            level: $user->level, 
            offices: $offices,
            permissions: $permissions
        );

        Cache::put(
            key: self::CACHE_PREFIX . $user->id, 
            value: $userDto, 
            ttl: self::CACHE_TTL
        );
        
        return $userDto;
    }

    public function getOfficeUser(int $user_id): array
    {
        $rows = OfficeUser::get($user_id);
        return [
            'ids' => $rows->pluck('id')->toArray(),
            'names' => $rows->pluck('name')->toArray(),
        ];
    }

    private function validateUserStatus(int $status): void
    {
        if ($status === StatusUser::INACTIVE->value) {
            throw new JsonResponseException(
                'Usuario inactivo. Contacte con el área de Soporte Informático para más información', 
                401
            );
        }

        if ($status === StatusUser::SUSPENDED->value) {
            throw new JsonResponseException(
                'Usuario suspendido. Contacte con el área de Soporte Informático para más información', 
                401
            );
        }
    }

    public static function clearUserCache(int $userId): bool
    {
        return Cache::forget(self::CACHE_PREFIX . $userId);
    }

    public function getCachedUser(int $userId): ?UserAuthDTO
    {
        return Cache::get(self::CACHE_PREFIX . $userId);
    }
}