<?php

namespace Modules\Auth\Shared\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Auth\Services\PermissionChecker;
use Modules\Auth\Infrastructure\Context\RequestContext;

/**
 * Clase base intermedia que extiende FormRequest con funcionalidad de control de acceso.
 * Usa PermissionChecker del módulo Auth para verificar permisos por nombre.
 * Reemplaza el Trait AccessControlServices con una implementación más centralizada.
 */
abstract class BaseFormRequest extends FormRequest
{
    protected function verifyPermission(
        string|array $permissionNames,
        bool $strict = false
    ): bool {
        
        $permissionIds = $this->normalizePermissionIds($this->attributes->get('permissions'));
        $permissionChecker = new PermissionChecker();
        $context = $this->createRequestContext($permissionIds);

        $permissionNames = is_array($permissionNames) ? $permissionNames : [$permissionNames];

        if ($strict) {
            return $permissionChecker->hasAllPermissionsByName($context, $permissionNames);
        }

        if (count($permissionNames) === 1) {
            return $permissionChecker->hasPermissionByName($context, $permissionNames[0]);
        }

        return $permissionChecker->hasAnyPermissionByName($context, $permissionNames);
    }


    private function normalizePermissionIds(string|array $permissionIds): array
    {
        if (is_array($permissionIds)) {
            return $permissionIds;
        }

        // Si es string vacío, retornar array vacío
        if (empty($permissionIds)) {
            return [];
        }

        $ids = explode(',', $permissionIds);
        return array_map(function ($id) {
            return (int) trim($id);
        }, $ids);
    }

    /**
     * Crea un RequestContext a partir de los datos disponibles en la request.
     */
    private function createRequestContext(array $permissionIds): RequestContext
    {
        return new RequestContext(
            userId: (int) $this->attributes->get('user_id', 0),
            level: (int) $this->attributes->get('level', 0),
            dni: (string) $this->attributes->get('dni', ''),
            permissions: $permissionIds,
            officeIds: $this->normalizeArrayAttribute('office_ids'),
            localeIds: $this->normalizeArrayAttribute('locale_ids')
        );
    }

    /**
     * Normaliza un atributo que puede venir como string o array.
     */
    private function normalizeArrayAttribute(string $key): array
    {
        $value = $this->attributes->get($key, []);

        if (is_array($value)) {
            return $value;
        }

        if (empty($value)) {
            return [];
        }

        // Si es string JSON o separado por coma
        if (str_starts_with($value, '[')) {
            return json_decode($value, true) ?? [];
        }

        return array_map(function ($id) {
            return (int) trim($id);
        }, explode(',', $value));
    }
}
