<?php

namespace Modules\Accounts\Applications;

use Illuminate\Support\Facades\Storage;
use Modules\Accounts\Repositories\PersonalDataExtraRepository;
use Infrastructure\Exceptions\JsonResponseException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class GetCertificateFileCase
{
    public function __construct(
        private PersonalDataExtraRepository $repository,
    ) {}

    public function exec(string $certificateType, int $authenticatedUserId): BinaryFileResponse
    {
        // Get the personal data
        $personalData = $this->repository->getByUserIdOrFail($authenticatedUserId);

        // Verify that the user can only access their own certificates
        if ($personalData->user_id !== $authenticatedUserId) {
            throw new JsonResponseException('No tienes permiso para acceder a este archivo', 403);
        }

        // Get the file path based on certificate type
        $filePath = $this->getFilePath($personalData, $certificateType);

        // Verify file exists
        if (!Storage::disk('private')->exists($filePath)) {
            throw new JsonResponseException('El archivo solicitado no existe', 404);
        }

        // Get the full path
        $fullPath = Storage::disk('private')->path($filePath);

        // Return binary file response for download
        return response()->download($fullPath);
    }

    private function getFilePath($personalData, string $certificateType): string
    {
        return match ($certificateType) {
            'disability' => $personalData->file_cert_disability,
            'army' => $personalData->file_cert_army,
            'professional_credentials' => $personalData->file_cert_professional_credentials,
            default => throw new JsonResponseException('Tipo de certificado inválido', 400),
        };
    }
}
