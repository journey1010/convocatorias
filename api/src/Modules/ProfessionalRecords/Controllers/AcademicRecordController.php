<?php

namespace Modules\ProfessionalRecords\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\{JsonResponse, BinaryFileResponse};
use Modules\Auth\Infrastructure\Context\RequestContextResolver;
use Modules\ProfessionalRecords\Requests\{
    CreateAcademicRecordRequest,
    UpdateAcademicRecordRequest,
    DeleteAcademicRecordRequest,
    ListAcademicRecordsRequest
};
use Modules\ProfessionalRecords\Applications\{
    CreateAcademicRecordCase,
    UpdateAcademicRecordCase,
    DeleteAcademicRecordCase,
    ListAcademicRecordsCase,
    GetProfessionalFileCase
};
use Modules\ProfessionalRecords\Applications\Dtos\{CreateAcademicRecordDto, UpdateAcademicRecordDto};

class AcademicRecordController extends \Modules\Shared\Controllers\Controller
{
    public function create(CreateAcademicRecordRequest $request, CreateAcademicRecordCase $case): JsonResponse
    {
        $ctx = RequestContextResolver::fromRequest($request);

        $dto = new CreateAcademicRecordDto(
            user_id: $ctx->userId,
            specialization_area_id: $request->input('specialization_area_id'),
            level: $request->input('level'),
            status: $request->input('status'),
            start_date: $request->input('start_date'),
            end_date: $request->input('end_date'),
            description: $request->input('description'),
            file: $request->file('file'),
        );

        $result = $case->exec($dto);

        return response()->json($result, 201);
    }

    public function update(UpdateAcademicRecordRequest $request, UpdateAcademicRecordCase $case): JsonResponse
    {
        $ctx = RequestContextResolver::fromRequest($request);

        $dto = new UpdateAcademicRecordDto(
            id: $request->input('id'),
            specialization_area_id: $request->input('specialization_area_id'),
            level: $request->input('level'),
            status: $request->input('status'),
            start_date: $request->input('start_date'),
            end_date: $request->input('end_date'),
            description: $request->input('description'),
            file: $request->file('file'),
        );

        $result = $case->exec($ctx->userId, $dto);

        return response()->json($result);
    }

    public function delete(DeleteAcademicRecordRequest $request, DeleteAcademicRecordCase $case): JsonResponse
    {
        $ctx = RequestContextResolver::fromRequest($request);
        $case->exec($request->input('id'), $ctx->userId);

        return response()->json(['message' => 'Registro académico eliminado exitosamente']);
    }

    public function list(ListAcademicRecordsRequest $request, ListAcademicRecordsCase $case): JsonResponse
    {
        $ctx = RequestContextResolver::fromRequest($request);
        $result = $case->exec($ctx->userId);

        return response()->json($result);
    }

    public function GetFile(Request $request, string $filePath, GetProfessionalFileCase $case): BinaryFileResponse
    {
        $ctx = RequestContextResolver::fromRequest($request);
        return $case->exec($filePath, $ctx);
    }
}
