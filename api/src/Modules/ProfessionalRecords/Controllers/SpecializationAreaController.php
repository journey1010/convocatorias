<?php

namespace Modules\ProfessionalRecords\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Modules\ProfessionalRecords\Requests\{
    CreateSpecializationAreaRequest,
    UpdateSpecializationAreaRequest
};
use Modules\ProfessionalRecords\Applications\{
    CreateSpecializationAreaCase,
    UpdateSpecializationAreaCase,
    ListSpecializationAreasCase
};
use Modules\ProfessionalRecords\Applications\Dtos\CreateSpecializationAreaDto;

class SpecializationAreaController extends \Modules\Shared\Controllers\Controller
{
    public function create(CreateSpecializationAreaRequest $request, CreateSpecializationAreaCase $case): JsonResponse
    {
        $dto = new CreateSpecializationAreaDto(
            name: $request->input('name'),
        );

        $result = $case->exec($dto);

        return response()->json($result, 201);
    }

    public function update(UpdateSpecializationAreaRequest $request, UpdateSpecializationAreaCase $case): JsonResponse
    {
        $result = $case->exec($request->input('id'), $request->input('name'));

        return response()->json($result);
    }

    public function list(ListSpecializationAreasCase $case): JsonResponse
    {
        $result = $case->exec();

        return response()->json(['items' => $result]);
    }
}
