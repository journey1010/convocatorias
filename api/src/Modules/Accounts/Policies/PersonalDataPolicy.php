<?php

namespace Modules\Accounts\Policies;

use Modules\Auth\Infrastructure\Context\RequestContext;
use Modules\Auth\Services\PermissionChecker;

class PersonalDataPolicy
{
    public function __construct(private PermissionChecker $permissionChecker) {}

    public function viewCertificate(RequestContext $context, int $ownerFile): bool
    {
        if ($this->permissionChecker->hasPermissionByName($context, 'cv.evaluation')) {
            return true;
        }

        if ($ownerFile == $context->userId) {
            return true;
        }

        return false;
    }
}
