<?php

namespace Modules\ProfessionalRecords\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Modules\Auth\Infrastructure\Context\RequestContextResolver;
use Modules\ProfessionalRecords\Applications\GetProfessionalFileCase;

class SharedController extends \Modules\Shared\Controllers\Controller
{
    public function GetFile(Request $request, string $filePath, GetProfessionalFileCase $case): BinaryFileResponse
    {
        $ctx = RequestContextResolver::fromRequest($request);
        return $case->exec($filePath, $ctx);
    }
}