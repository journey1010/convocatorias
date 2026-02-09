<?php

namespace Modules\ProfessionalRecords\Applications;

use Illuminate\Support\Facades\Storage;
use Infrastructure\Exceptions\JsonResponseException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Modules\Auth\Infrastructure\Context\RequestContext;
use Modules\ProfessionalRecords\Policies\ProfessionalRecordsPolicy;

class GetProfessionalFileCase
{
    public function __construct(private ProfessionalRecordsPolicy $policy) {}

    public function exec(string $requestedPath, RequestContext $context): BinaryFileResponse
    {
        $fileName = basename($requestedPath);
        $parts = explode('_', $fileName);

        // File naming: {type}_{user_id}_{timestamp}_{uniqid}.{extension}
        if (count($parts) < 2 || !ctype_digit($parts[1])) {
            throw new JsonResponseException('El archivo no existe.', 400);
        }

        $fileOwnerId = (int) $parts[1];

        if (!$this->policy->viewFile($context, $fileOwnerId)) {
            throw new JsonResponseException('No tiene permiso para ver este documento.', 403);
        }

        if (!Storage::disk('private')->exists($requestedPath)) {
            throw new JsonResponseException('Archivo no encontrado en el servidor.', 404);
        }

        return response()->file(Storage::disk('private')->path($requestedPath));
    }
}
