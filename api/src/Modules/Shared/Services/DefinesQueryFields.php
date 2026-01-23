<?php

namespace Modules\Shared\Services;

/**
 * Trait para definir campos de queries de forma compacta
 *
 * @property-read string $ALIAS
 */
trait DefinesQueryFields
{
    /**
     * Define múltiples campos simples de la tabla principal
     */
    protected static function defineSimpleFields(array $columns): array
    {
        $fields = [];
        foreach ($columns as $column) {
            $fields[$column] = [
                'table'  => static::$ALIAS,
                'column' => $column
            ];
        }
        return $fields;
    }

    /**
     * Define campos para una relación JOIN con soporte multinivel.
     * Permite relacionar desde cualquier tabla (principal u otra ya joinada).
     *
     * @param string $fieldPrefix     Prefijo para los campos resultantes (ej: 'department', 'manager')
     * @param string $sourceColumn    Columna en la tabla origen para el JOIN (ej: 'department_id', 'code', 'email')
     * @param string $targetTable     Tabla destino (ej: 'departments', 'users')
     * @param string $targetAlias     Alias SQL para la tabla destino (ej: 'dept', 'mgr')
     * @param string $targetColumn    Columna en tabla destino para el JOIN (default: 'id')
     * @param string $displayColumn   Columna a mostrar (default: 'name')
     * @param string $mode            Modo de retorno: 'both' | 'id' | 'name'
     * @param string|null $sourceTable Tabla origen (null = tabla principal, string = alias de tabla joinada)
     * 
     * @return array Configuración de campos relacionados
     */
    protected static function defineRelation(
        string $fieldPrefix,
        string $sourceColumn,
        string $targetTable,
        string $targetAlias,
        string $targetColumn = 'id',
        string $displayColumn = 'name',
        string $mode = 'both',
        ?string $sourceTable = null
    ): array {
        // Si no se especifica origen, usar la tabla principal
        $sourceAlias = $sourceTable ?? static::$ALIAS;
        
        // Configuración del JOIN: source.column = target.column
        $joinConfig = [
            "{$targetTable} as {$targetAlias}",
            "{$targetAlias}.{$targetColumn}",
            '=',
            "{$sourceAlias}.{$sourceColumn}"
        ];

        $fields = [];

        // Campo de la columna target (puede ser id, code, etc.)
        if (in_array($mode, ['both', 'id'], true)) {
            $fields["{$fieldPrefix}_{$targetColumn}"] = [
                'table'  => $targetAlias,
                'column' => $targetColumn,
                'alias'  => "{$fieldPrefix}_{$targetColumn}",
                'joins'  => $joinConfig
            ];
        }

        // Campo de visualización (nombre u otro)
        if (in_array($mode, ['both', 'name'], true)) {
            $fields[$fieldPrefix] = [
                'table'  => $targetAlias,
                'column' => $displayColumn,
                'alias'  => $fieldPrefix,
                'joins'  => $joinConfig
            ];
        }

        return $fields;
    }

    /**
     * Define un campo con expresión SQL (ej: ST_X, CONCAT, etc)
     */
    protected static function defineExpression(string $fieldName, string $expression, ?string $alias = null): array
    {
        return [
            $fieldName => [
                'expr'  => $expression,
                'alias' => $alias ?? $fieldName
            ]
        ];
    }
}