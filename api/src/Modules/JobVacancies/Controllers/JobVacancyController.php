<?php

namespace Modules\JobVacancies\Controllers;

use Illuminate\Http\JsonResponse;
use Modules\Auth\Infrastructure\Context\RequestContextResolver;
use Modules\JobVacancies\Requests\{
    CreateJobVacancyRequest,
    UpdateJobVacancyRequest,
    ListJobVacanciesRequest,
    SearchProfilesRequest,
    UpdateStatusRequest
};
use Modules\JobVacancies\Applications\{
    CreateJobVacancyCase,
    UpdateJobVacancyCase,
    ListJobVacanciesCase,
    GetJobVacancyDetailsCase,
    UpdateVacancyStatusCase,
    PaginationProfilesCase
};
use Modules\JobVacancies\Applications\Dtos\{
    CreateJobVacancyDto, 
    UpdateJobVacancyDto, 
    JobProfileSearchDto
};
use Modules\JobVacancies\Enums\VacancyStatus;

class JobVacancyController extends \Modules\Shared\Controllers\Controller
{
    public function create(CreateJobVacancyRequest $request, CreateJobVacancyCase $case): JsonResponse
    {
        $ctx = RequestContextResolver::fromRequest($request);

        $inputs = $request->validated();
    
        $dto = new CreateJobVacancyDto(
            created_by: $ctx->userId,
            locale_id: $ctx->localeId,
            title: $inputs['title'],
            status: VacancyStatus::PUBLICADA,
            mode: $inputs['mode'],
            start_date: $inputs['start_date'],
            close_date: $inputs['close_date'],
            profiles: $inputs['profiles'],
            doc_base_file: $inputs['doc_base_file'],
        );

        $result = $case->exec($dto);

        return response()->json($result, 201);
    }

    public function search(SearchProfilesRequest $request, PaginationProfilesCase $case): JsonResponse
    {
        $dto = new JobProfileSearchDto(
            page: $request->input('page'),
            per_page: $request->input('per_page'),
            locale_id: $request->input('locale_id'),
            office_id: $request->input('office_id'),
            specialization_area_id: $request->input('specialization_area_id'),
            status: $request->input('status'),
            salary: $request->input('salary'),
            title: $request->input('title'),
            code_profile: $request->input('code_profile')
        );

        $result = $case->exec($dto);

        return response()->json(['items' => $result]);
    }

    public function update(UpdateJobVacancyRequest $request, UpdateJobVacancyCase $case): JsonResponse
    {
        $ctx = RequestContextResolver::fromRequest($request);

        $dto = new UpdateJobVacancyDto(
            id: $request->input('id'),
            title: $request->input('title'),
            status: $request->input('status'),
            mode: $request->boolean('mode'),
            start_date: $request->input('start_date'),
            close_date: $request->input('close_date'),
        );

        $result = $case->exec($dto, $ctx);

        return response()->json($result);
    }

    public function list(ListJobVacanciesRequest $request, ListJobVacanciesCase $case): JsonResponse
    {
        // Intentar obtener contexto, puede ser null para usuarios públicos
        try {
            $ctx = RequestContextResolver::fromRequest($request);
        } catch (\Exception $e) {
            $ctx = null;
        }

        $filters = $request->only(['status', 'locale_id']);
        $result = $case->exec($ctx, $filters);

        return response()->json(['items' => $result]);
    }

    public function show(int $id, GetJobVacancyDetailsCase $case): JsonResponse
    {
        // Intentar obtener contexto, puede ser null para usuarios públicos
        try {
            $ctx = RequestContextResolver::fromRequest(request());
        } catch (\Exception $e) {
            $ctx = null;
        }

        $result = $case->exec($id, $ctx);

        return response()->json($result);
    }

    public function updateStatus(UpdateStatusRequest $request, UpdateVacancyStatusCase $case): JsonResponse
    {
        $ctx = RequestContextResolver::fromRequest($request);

        $result = $case->exec(
            vacancyId: $request->input('id'),
            newStatus: $request->input('status'),
            ctx: $ctx
        );

        return response()->json($result);
    }
}