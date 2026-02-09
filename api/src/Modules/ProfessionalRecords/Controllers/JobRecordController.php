<?php

namespace Modules\ProfessionalRecords\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\{JsonResponse, BinaryFileResponse};
use Modules\Auth\Infrastructure\Context\RequestContextResolver;
use Modules\ProfessionalRecords\Requests\{
    CreateJobRecordRequest,
    UpdateJobRecordRequest,
    DeleteJobRecordRequest,
    ListJobRecordsRequest
};
use Modules\ProfessionalRecords\Applications\{
    CreateJobRecordCase,
    UpdateJobRecordCase,
    DeleteJobRecordCase,
    ListJobRecordsCase,
    GetProfessionalFileCase
};
use Modules\ProfessionalRecords\Applications\Dtos\{CreateJobRecordDto, UpdateJobRecordDto};

class JobRecordController extends \Modules\Shared\Controllers\Controller
{
    public function create(CreateJobRecordRequest $request, CreateJobRecordCase $case): JsonResponse
    {
        $ctx = RequestContextResolver::fromRequest($request);

        $dto = new CreateJobRecordDto(
            user_id: $ctx->userId,
            entity_name: $request->input('entity_name'),
            type: $request->input('type'),
            specialization_area: $request->input('specialization_area'),
            status: $request->input('status'),
            description: $request->input('description'),
            start_date: $request->input('start_date'),
            end_date: $request->input('end_date'),
            file: $request->file('file'),
        );

        $result = $case->exec($dto);

        return response()->json($result, 201);
    }

    public function update(UpdateJobRecordRequest $request, UpdateJobRecordCase $case, int $id): JsonResponse
    {
        $ctx = RequestContextResolver::fromRequest($request);

        $dto = new UpdateJobRecordDto(
            entity_name: $request->input('entity_name'),
            type: $request->input('type'),
            specialization_area: $request->input('specialization_area'),
            status: $request->input('status'),
            description: $request->input('description'),
            start_date: $request->input('start_date'),
            end_date: $request->input('end_date'),
            file: $request->file('file'),
        );

        $result = $case->exec($id, $ctx->userId, $dto);

        return response()->json($result);
    }

    public function delete(DeleteJobRecordRequest $request, DeleteJobRecordCase $case, int $id): JsonResponse
    {
        $case->exec($id);

        return response()->json(['message' => 'Registro laboral eliminado exitosamente']);
    }

    public function list(ListJobRecordsRequest $request, ListJobRecordsCase $case): JsonResponse
    {
        $ctx = RequestContextResolver::fromRequest($request);
        $result = $case->exec($ctx->userId);

        return response()->json($result);
    }

    public function downloadFile(Request $request, string $filePath, GetProfessionalFileCase $case): BinaryFileResponse
    {
        $ctx = RequestContextResolver::fromRequest($request);
        return $case->exec($filePath, $ctx);
    }
}
