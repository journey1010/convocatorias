<?php

namespace Infrastructure\Services;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GlobalResponseHandlerException {

    public static function handler(\Throwable $e, ?Request $request = null): JsonResponse
    {
        if ($e instanceof NotFoundHttpException) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return response()->json(['message' => 'Method Not Allowed'], 405);
        }

        if ($e instanceof ModelNotFoundException) {
            return response()->json(['message' => 'Resource Not Found'], 404);
        }

        $message = ['message' => 'Servicio no disponible.'];

        if (config('app.debug')) {
            $message['error'] = [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'code'    => $e->getCode(),
            ];
        }

        Log::error($e->getMessage(), [
            'exception'     => get_class($e),
            'file'          => $e->getFile(),
            'line'          => $e->getLine(),
            'code'          => $e->getCode(),
            'previous'      => $e->getPrevious() ? get_class($e->getPrevious()) : null,
            'trace_snippet' => collect($e->getTrace())->take(5)->toArray(),
            'timestamp'     => now()->toIso8601String(),
            'request'       => $request?->all(),
        ]);

        return response()->json($message, 503);
    }
}