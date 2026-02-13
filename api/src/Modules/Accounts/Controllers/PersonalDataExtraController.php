<?php

namespace Modules\Accounts\Controllers;

use Illuminate\Http\Request;
use Modules\Accounts\Requests\UpsertPersonalDataExtraRequest;
use Modules\Accounts\Applications\{
    UpsertPersonalDataExtraCase,
    GetCertificateFileCase,
    Dtos\PersonalDataExtraResponseDto
};
use Modules\Accounts\Repositories\PersonalDataExtraRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Modules\Auth\Infrastructure\Context\RequestContextResolver;

class PersonalDataExtraController extends \Modules\Shared\Controllers\Controller
{
    public function upsert(UpsertPersonalDataExtraRequest $request, UpsertPersonalDataExtraCase $case): JsonResponse
    {
        $ctx = RequestContextResolver::fromRequest($request);

        $dto = $this->mapRequestToUpsertDto($request, $ctx->userId);
        $result = $case->exec($dto, $ctx->userId);

        return response()->json($result, 201);
    }

    public function show(Request $request, PersonalDataExtraRepository $repository): JsonResponse
    {
        $ctx = RequestContextResolver::fromRequest($request);
        $personalData = $repository->findByUserId($ctx->userId);
        $response = PersonalDataExtraResponseDto::fromModel($personalData);
        return response()->json($response);
    }

    public function downloadCertificate(Request $request, string $certificateType, GetCertificateFileCase $case): BinaryFileResponse
    {
        $context = RequestContextResolver::fromRequest($request);
        return $case->exec($certificateType, $context);
    }

    private function mapRequestToUpsertDto(UpsertPersonalDataExtraRequest $request, int $userId)
    {
        return new \Modules\Accounts\Applications\Dtos\StorePersonalDataExtraDto(
            user_id: $userId,
            department_id: $request->input('department_id'),
            province_id: $request->input('province_id'),
            district_id: $request->input('district_id'),
            ruc: $request->input('ruc'),
            address: $request->input('address'),
            birthday: $request->input('birthday'),
            gender: $request->input('gender'),
            file_dni: $request->file('file_dni'),
            have_cert_disability: $request->boolean('have_cert_disability'),
            file_cert_disability: $request->file('file_cert_disability'),
            have_cert_army: $request->boolean('have_cert_army'),
            file_cert_army: $request->file('file_cert_army'),
            have_cert_professional_credentials: $request->boolean('have_cert_professional_credentials'),
            file_cert_professional_credentials: $request->file('file_cert_professional_credentials'),
            is_active_cert_professional_credentials: $request->boolean('is_active_cert_professional_credentials'),
        );
    }
}
