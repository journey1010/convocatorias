<?php 

namespace Modules\User\Applications;

use Illuminate\Http\Request;
use Infrastructure\Exceptions\JsonResponseException;
use Modules\User\Models\User;
use Modules\User\Models\OfficeUser;
use Modules\User\Services\UserAuth;

class UpdateUserCase {

    public function exec(Request $request): void
    {
        $inputs = array_filter($request->validated(), fn($value) => !is_null($value));

        $user = User::findOrFail($inputs['id']);

        // Verificar conflictos solo si email o dni están presentes
        if (isset($inputs['email']) || isset($inputs['dni'])) {
            $query = User::where('id', '!=', $inputs['id']);
            
            if (isset($inputs['email'])) {
                $query->where('email', $inputs['email']);
            }
            
            if (isset($inputs['dni'])) {
                $query->orWhere(fn($q) => $q->where('id', '!=', $inputs['id'])->where('dni', $inputs['dni']));
            }
            
            $userConflict = $query->first(['email', 'dni']);

            if ($userConflict) {
                if (isset($inputs['email']) && $userConflict->email === $inputs['email']) {
                    throw new JsonResponseException('El correo electrónico ingresado ya está en uso', 422);
                }
                if (isset($inputs['dni']) && $userConflict->dni === $inputs['dni']) {
                    throw new JsonResponseException('Este DNI ya se encuentra en uso', 422);
                }
            }
        }

        if(isset($inputs['office_id'])){
            OfficeUser::updateData($inputs['id'], $inputs['office_id']);
        }
        
        UserAuth::clearUserCache($inputs['id']);
        unset($inputs['id']);

        if (isset($inputs['roles'])) {
            $user->syncRoles($inputs['roles']);
            unset($inputs['roles']);
        }

        if (isset($inputs['permissions'])) {
            $user->syncPermissions($inputs['permissions']);
            unset($inputs['permissions']);
        }
        
        if (!empty($inputs)) {
            $user->fill($inputs)->save();
        }
    }
}