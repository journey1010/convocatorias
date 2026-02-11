<?php

namespace Modules\JobVacancies\Controllers;

use Illuminate\Http\JsonResponse;
use Modules\Auth\Infrastructure\Context\RequestContextResolver;
use Modules\JobVacancies\Requests\ManageProfilesRequest;
use Modules\JobVacancies\Applications\ManageJobProfilesCase;

class JobProfileController extends \Modules\Shared\Controllers\Controller
{
    public function manage(ManageProfilesRequest $request, ManageJobProfilesCase $case): JsonResponse
    {
        $ctx = RequestContextResolver::fromRequest($request);

        $result = $case->exec(
            vacancyId: $request->input('job_vacancy_id'),
            profilesData: $request->input('profiles'),
            ctx: $ctx
        );

        return response()->json(['profiles' => $result]);
    }
}
