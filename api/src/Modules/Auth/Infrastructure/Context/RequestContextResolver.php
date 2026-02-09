<?php

namespace Modules\Auth\Infrastructure\Context;

use Illuminate\Http\Request;

/**
 * Helper para extraer RequestContext del Request.
 * Este es el ÚNICO punto donde se accede a $request->attributes.
 * 
 */
final class RequestContextResolver
{
    private const ATTRIBUTE_KEY = 'request_context';

    /**
     * Obtiene el RequestContext desde el request.
     */
    public static function fromRequest(Request $request): RequestContext
    {
        // Si ya existe en el request, reutilizarlo
        if ($request->attributes->has(self::ATTRIBUTE_KEY)) {
            return $request->attributes->get(self::ATTRIBUTE_KEY);
        }

        // Construir desde los attributes del JWT
        $context = new RequestContext(
            userId: (int) $request->attributes->get('sub', 0),
            level: (int) $request->attributes->get('level', 666),
            dni: (string) $request->attributes->get('dni', ''),
            permissions: self::parseIntArray($request->attributes->get('permissions', '')),
            officeIds: self::parseIntArray($request->attributes->get('office_ids', '')),
            localeIds: self::parseIntArray($request->attributes->get('locale_ids', '')),
        );

        // Cachear en el request para futuras llamadas
        $request->attributes->set(self::ATTRIBUTE_KEY, $context);

        return $context;
    }

    /**
     * Obtiene datos para refresh token (solo sub y exp).
     */
    public static function getRefreshData(Request $request): array
    {
        return [
            'userId' => (int) $request->attributes->get('sub', 0),
            'exp' => (int) $request->attributes->get('exp', 0),
        ];
    }

    /**
     * Convierte string separado por comas a array de enteros.
     */
    private static function parseIntArray(string|array|null $value): array
    {
        if (empty($value)) {
            return [];
        }

        if (is_array($value)) {
            return array_map('intval', $value);
        }

        return array_map('intval', explode(',', $value));
    }
}
