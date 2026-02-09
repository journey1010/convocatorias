<?php

namespace Modules\Accounts\Applications;

use Illuminate\Support\Facades\Storage;
use Infrastructure\Exceptions\JsonResponseException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Modules\Auth\Infrastructure\Context\RequestContext;
use Modules\Accounts\Policies\PersonalDataPolicy;

class GetCertificateFileCase
{
    public function __construct(private PersonalDataPolicy $policy) {}

    public function exec(string $requestedPath, RequestContext $context): BinaryFileResponse
    {
        $fileName = basename($requestedPath);
        $parts = explode('_', $fileName);

        if (count($parts) < 2 || !ctype_digit($parts[1])) {
            throw new JsonResponseException('El archivo no existe.', 400);
        }

        $fileOwnerId = (int) $parts[1];

        if (!$this->policy->viewCertificate($context, $fileOwnerId)) {
            throw new JsonResponseException('No tiene permiso para ver este documento.', 403);
        }

        if (!Storage::disk('private')->exists($requestedPath)) {
            throw new JsonResponseException('Archivo no encontrado en el servidor.', 404);
        }

        return response()->file(Storage::disk('private')->path($requestedPath));
    }
}
