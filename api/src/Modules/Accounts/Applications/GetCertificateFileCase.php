<?php

namespace Modules\Accounts\Applications;

use Illuminate\Support\Facades\Storage;
use Modules\Accounts\Repositories\PersonalDataExtraRepository;
use Infrastructure\Exceptions\JsonResponseException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class GetCertificateFileCase
{
    public function __construct(private PersonalDataExtraRepository $repository) {}

    public function exec(string $requestedPath, int $authenticatedUserId): BinaryFileResponse
    {
        $fileName = basename($requestedPath);

        $fileType = explode('_', $fileName)[0];

        $columnMap = [
            'disability'               => 'file_cert_disability',
            'army'                     => 'file_cert_army',
            'professional'             => 'file_cert_professional_credentials',
        ];

        $column = $columnMap[$fileType] ?? null;

        // 4. Validar propiedad y existencia en la columna específica
        if (!$column || !$this->repository->existsPathInColumn($authenticatedUserId, $column, $requestedPath)) {
            throw new JsonResponseException('Acceso denegado o archivo no válido.', 403);
        }

        if (!Storage::disk('private')->exists($requestedPath)) {
            throw new JsonResponseException('Archivo no encontrado en el servidor.', 404);
        }

        return response()->download(Storage::disk('private')->path($requestedPath));
    }
}
