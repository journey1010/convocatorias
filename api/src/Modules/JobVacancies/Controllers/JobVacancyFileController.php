<?php

namespace Modules\JobVacancies\Controllers;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Storage;
use Modules\Auth\Infrastructure\Context\RequestContextResolver;
use Modules\JobVacancies\Requests\{AttachFileRequest, UpdateFileNameRequest};
use Modules\JobVacancies\Applications\{AttachFileToVacancyCase, UpdateVacancyFileNameCase};
use Modules\JobVacancies\Applications\Dtos\JobVacancyFileDto;

class JobVacancyFileController extends \Modules\Shared\Controllers\Controller
{
    public function attach(AttachFileRequest $request, AttachFileToVacancyCase $case): JsonResponse
    {
        $ctx = RequestContextResolver::fromRequest($request);

        $dto = new JobVacancyFileDto(
            id: null,
            job_vacancy_id: $request->input('job_vacancy_id'),
            file: $request->file('file'),
            name: $request->input('name'),
        );

        $result = $case->exec($dto, $ctx);

        return response()->json($result, 201);
    }

    public function updateName(UpdateFileNameRequest $request, UpdateVacancyFileNameCase $case): JsonResponse
    {
        $ctx = RequestContextResolver::fromRequest($request);

        $result = $case->exec(
            fileId: $request->input('id'),
            newName: $request->input('name'),
            ctx: $ctx
        );

        return response()->json($result);
    }

    public function download(string $filePath): BinaryFileResponse
    {
        // Verificar que el archivo existe
        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'Archivo no encontrado');
        }

        $fullPath = Storage::disk('public')->path($filePath);
        
        return response()->file($fullPath);
    }
}
