<?php

namespace Modules\ProfessionalRecords\Policies;

use Modules\Auth\Infrastructure\Context\RequestContext;
use Modules\Auth\Services\PermissionChecker;

class ProfessionalRecordsPolicy
{
    public function __construct(private PermissionChecker $permissionChecker) {}

    public function viewFile(RequestContext $context, int $ownerId): bool
    {
        // Users with cv.evaluation permission can view all files
        if ($this->permissionChecker->hasPermissionByName($context, 'cv.evaluation')) {
            return true;
        }

        // Users can view their own files
        if ($ownerId == $context->userId) {
            return true;
        }

        return false;
    }

    public function manageOwnRecords(RequestContext $context, int $ownerId): bool
    {
        return $ownerId == $context->userId;
    }

    public function manageSpecializationAreas(RequestContext $context): bool
    {
        // Only users with specific permission can manage specialization areas
        return $this->permissionChecker->hasPermissionByName($context, 'specialization_areas.manage');
    }
}
