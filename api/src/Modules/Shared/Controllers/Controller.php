<?php

namespace Modules\Shared\Controllers;

use Exception;
use InvalidArgumentException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Infrastructure\Services\GlobalResponseHandlerException;

abstract class Controller
{
    public function logError($class, InvalidArgumentException $e, $function): void
    {
        Log::error($class  . ', method: ' . $function .  ': ' . $e->getMessage());   
    }

    public function defaultResponse(?Exception $e = null): JsonResponse
    {
        return GlobalResponseHandlerException::handler($e);
    }
}
