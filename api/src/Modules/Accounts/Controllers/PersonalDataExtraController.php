<?php

namespace Modules\Accounts\Controllers;

use Illuminate\Http\Request;
use Modules\Accounts\Requests\{
    StorePersonalDataExtraRequest,
    UpdatePersonalDataExtraRequest,
};
use Modules\Accounts\Applications\{
    StorePersonalDataExtraCase,
    UpdatePersonalDataExtraCase,
    GetCertificateFileCase,
};
use Modules\Accounts\Repositories\PersonalDataExtraRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Infrastructure\Exceptions\JsonResponseException;

class PersonalDataExtraController extends \Modules\Shared\Controllers\Controller
{
    public function store(StorePersonalDataExtraRequest $request, StorePersonalDataExtraCase $case    ): JsonResponse {
        $authenticatedUserId = $this->getAuthenticatedUserId($request);

        $dto = $this->mapRequestToStoreDto($request, $authenticatedUserId);
        $result = $case->exec($dto, $authenticatedUserId);

        return response()->json($result, 201);
    }

    public function show(
        Request $request,
        PersonalDataExtraRepository $repository
    ): JsonResponse {
        $authenticatedUserId = $this->getAuthenticatedUserId($request);
        $personalData = $repository->getByUserIdOrFail($authenticatedUserId);

        return response()->json($personalData);
    }

    public function update(
        UpdatePersonalDataExtraRequest $request,
        UpdatePersonalDataExtraCase $case
    ): JsonResponse {
        $authenticatedUserId = $this->getAuthenticatedUserId($request);

        $dto = $this->mapRequestToUpdateDto($request, $authenticatedUserId);
        $result = $case->exec($dto, $authenticatedUserId);

        return response()->json($result);
    }

    public function downloadCertificate(
        Request $request,
        string $certificateType,
        GetCertificateFileCase $case
    ): BinaryFileResponse {
        $authenticatedUserId = $this->getAuthenticatedUserId($request);
        
        return $case->exec($certificateType, $authenticatedUserId);
    }

    private function getAuthenticatedUserId(Request $request): int
    {
        $userId = $request->attributes->get('sub');

        if (!$userId) {
            throw new JsonResponseException('Usuario no autenticado', 401);
        }

        return $userId;
    }

    private function mapRequestToStoreDto(StorePersonalDataExtraRequest $request, int $userId)
    {
        return new \Modules\Accounts\Applications\Dtos\StorePersonalDataExtraDto(
            user_id: $userId,
            department_id: $request->input('department_id'),
            province_id: $request->input('province_id'),
            district_id: $request->input('district_id'),
            address: $request->input('address'),
            birthday: $request->input('birthday'),
            genere: $request->input('genere'),
            have_cert_disability: $request->boolean('have_cert_disability'),
            file_cert_disability: $request->file('file_cert_disability'),
            have_cert_army: $request->boolean('have_cert_army'),
            file_cert_army: $request->file('file_cert_army'),
            have_cert_professional_credentials: $request->boolean('have_cert_professional_credentials'),
            file_cert_professional_credentials: $request->file('file_cert_professional_credentials'),
            is_active_cert_professional_credentials: $request->boolean('is_active_cert_professional_credentials'),
        );
    }

    private function mapRequestToUpdateDto(UpdatePersonalDataExtraRequest $request, int $userId)
    {
        return new \Modules\Accounts\Applications\Dtos\UpdatePersonalDataExtraDto(
            user_id: $userId,
            department_id: $request->input('department_id'),
            province_id: $request->input('province_id'),
            district_id: $request->input('district_id'),
            address: $request->input('address'),
            birthday: $request->input('birthday'),
            genere: $request->input('genere'),
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
