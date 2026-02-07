<?php

namespace Modules\Accounts\Policies;

use Modules\User\Services\UserAuthMeta;

class PersonalDataPolicy {

    public function viewCertificate(UserAuthMeta $user, int $ownerFile): bool
    {
        if($user->hasPermission('cv.evaluation')){
            return true;
        }


        if($ownerFile == $user->user_id){
            return true;
        }

        return false;
    }
}