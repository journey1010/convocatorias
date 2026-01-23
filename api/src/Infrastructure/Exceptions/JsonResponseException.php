<?php

namespace Infrastructure\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class JsonResponseException extends Exception
{
    public function render(): JsonResponse
    {
        $status = $this->getCode() > 0 ? $this->getCode() : 500;

        return response()->json([
            'message' => $this->getMessage() ?: 'Error inesperado',
        ], $status);
    }
}