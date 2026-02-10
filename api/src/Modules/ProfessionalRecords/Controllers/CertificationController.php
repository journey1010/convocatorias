<?php

namespace Modules\ProfessionalRecords\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\{JsonResponse, BinaryFileResponse};
use Modules\Auth\Infrastructure\Context\RequestContextResolver;
use Modules\ProfessionalRecords\Requests\{
    CreateCertificationRequest,
    UpdateCertificationRequest,
    DeleteCertificationRequest,
    ListCertificationsRequest
};
use Modules\ProfessionalRecords\Applications\{
    CreateCertificationCase,
    UpdateCertificationCase,
    DeleteCertificationCase,
    ListCertificationsCase,
    GetProfessionalFileCase
};
use Modules\ProfessionalRecords\Applications\Dtos\{CreateCertificationDto, UpdateCertificationDto};

class CertificationController extends \Modules\Shared\Controllers\Controller
{
    public function create(CreateCertificationRequest $request, CreateCertificationCase $case): JsonResponse
    {
        $ctx = RequestContextResolver::fromRequest($request);

        $dto = new CreateCertificationDto(
            user_id: $ctx->userId,
            name: $request->input('name'),
            year: $request->input('year'),
            hours: $request->input('hours'),
            file: $request->file('file'),
        );

        $result = $case->exec($dto);

        return response()->json($result, 201);
    }

    public function update(UpdateCertificationRequest $request, UpdateCertificationCase $case): JsonResponse
    {
        $ctx = RequestContextResolver::fromRequest($request);

        $dto = new UpdateCertificationDto(
            name: $request->input('name'),
            year: $request->input('year'),
            hours: $request->input('hours'),
            file: $request->file('file'),
        );

        $result = $case->exec($request->input('id'), $ctx->userId, $dto);

        return response()->json($result);
    }

    public function delete(DeleteCertificationRequest $request, DeleteCertificationCase $case): JsonResponse
    {
        $ctx = RequestContextResolver::fromRequest($request);
        $case->exec($request->input('id'), $ctx->userId);

        return response()->json(['message' => 'Certificación eliminada exitosamente']);
    }

    public function list(ListCertificationsRequest $request, ListCertificationsCase $case): JsonResponse
    {
        $ctx = RequestContextResolver::fromRequest($request);
        $result = $case->exec($ctx->userId);

        return response()->json(['items' => $result]);
    }
}