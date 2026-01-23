<?php

namespace Modules\Shared\Services;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Clase base para construir consultas dinámicas altamente configurables.
 *
 * Permite:
 * - SELECT dinámico basado en definiciones de campos.
 * - JOINs automáticos según los campos requeridos.
 * - Filtros personalizados y genéricos.
 * - Ordenamiento dinámico.
 * - Integración con contextos del sistema (filtros obligatorios).
 *
 * Las clases hijas deben definir:
 * - TABLE
 * - ALIAS
 * - BASE_FIELDS
 * - FIELD_DEFS
*/

abstract class ListerBaseDinamicQueryBuilder
{
    /** @var string Nombre de la tabla principal */
    protected static string $TABLE = '';

    /** @var string Alias SQL de la tabla principal */
    protected static string $ALIAS = '';

    /** @var array Lista de campos base siempre incluidos en SELECT */
    protected static array $BASE_FIELDS = [];

    /**
     * @var array Definición de campos dinámicos:
     *
     * [
     *     'campo' => [
     *          'table'  => 'alias_tabla',
     *          'column' => 'columna_real',
     *          'alias'  => 'alias_final_select',
     *
     *          'joins' => [
     *              ['table as alias', 'alias.col', '=', 't.id'],
     *              ...
     *          ],
     *
     *          'expr'   => 'ST_X(t.geom)',
     *          'filter' => fn(Builder $query, mixed $value, string $op) => ...
     *     ]
     * ]
     */
    protected static array $FIELD_DEFS = [];

    // Cache para evitar reinicializar en cada instancia
    private static array $_initialized = [];


    public function bootJoins(Builder $query): void
    {  

    }

    // ==========================================================
    // QUERY BUILDER PRINCIPAL
    // ==========================================================

    /**
     * Construye el Query Builder dinámico.
     */
    public function buildQuery(
        ?array $select = null,
        ?string $orderBy = null,
        ?string $orderDirection = null,
        ?array $filters = null,
        ?array $context = null
    ): Builder {
        $select = $select ?? [];

        $query = DB::table(static::$TABLE . ' as ' . static::$ALIAS);

        $this->bootJoins($query);

        $fieldsNeeded = $this->extractFieldsNeeded($filters, $orderBy, $context);

        $this->applyJoins($query, $select, $fieldsNeeded);
        $this->applySelect($query, $select);
        $this->applyFilters($query, $filters, $context);
        $this->applyOrder($query, $orderBy, $orderDirection);

        return $query;
    }
    
    protected function applySelect(Builder $query, array $select): void
    {
        $columns = [];

        foreach (static::$BASE_FIELDS as $field) {
            $columns[] = $this->buildSelectColumn($field);
        }

        foreach ($select as $field) {
            $columns[] = $this->buildSelectColumn($field);
        }

        $query->select($columns);
    }

    protected function buildSelectColumn(string $field)
    {
        $def = static::getFieldDefs()[$field];

        $alias = $def['alias'] ?? $def['column'];

        if (isset($def['expr'])) {
            return DB::raw("{$def['expr']} as {$alias}");
        }

        return "{$def['table']}.{$def['column']} as {$alias}";
    }

    protected function applyJoins(Builder $query, array $select, array $filterFields): void
    {
        $needed = array_unique(array_merge($select, $filterFields));
        $added = [];

        foreach ($needed as $field) {
            $def = static::getFieldDefs()[$field] ?? null;

            if (!$def) {
                continue;
            }

            if (($def['table'] ?? null) === static::$ALIAS) {
                continue;
            }

            if (!isset($def['joins'])) {
                continue;
            }

            $joins = is_array($def['joins'][0]) ? $def['joins'] : [$def['joins']];

            foreach ($joins as $join) {
                $joinKey = $join[0];

                if (!isset($added[$joinKey])) {
                    $query->leftJoin(...$join);
                    $added[$joinKey] = true;
                }
            }
        }
    }

    /**
     * Filtros obligatorios del sistema según contexto.
     */
    protected function getSystemFilters(?array $context = null): array
    {
        return [];
    }

    protected function applyFilters(Builder $query, ?array $userFilters, ?array $context): void
    {
        $systemFilters = $this->getSystemFilters($context);

        $userMap = [];
        if ($userFilters) {
            foreach ($userFilters as $f) {
                $userMap[$f['field']] = $f;
            }
        }

        foreach ($systemFilters as $field => $callback) {
            $callback($query);
            unset($userMap[$field]);
        }

        foreach ($userMap as $filter) {
            $this->applyUserFilter($query, $filter);
        }
    }

    protected function applyUserFilter(Builder $query, array $filter): void
    {
        $field = $filter['field'];
        $fieldDefs = static::getFieldDefs();

        if (isset($fieldDefs[$field]['filter'])) {
            $fieldDefs[$field]['filter'](
                $query,
                $filter['value'],
                $filter['operator']
            );
            return;
        }

        $this->applyBasicFilter($query, $filter);
    }

    protected function applyBasicFilter(Builder $query, array $filter): void
    {
        $col = $this->resolveColumn($filter['field']);
        $op = strtoupper($filter['operator']);
        $val = $filter['value'];

        if ($op === 'IN') {
            $query->whereIn($col, $val);
            return;
        }

        if ($op === 'LIKE' && !str_contains($val, '%')) {
            $val = "%{$val}%";
        }

        $query->where($col, $op, $val);
    }

    protected function applyOrder(Builder $query, ?string $orderBy, ?string $orderDirection): void
    {
        if (!$orderBy) {
            $query->orderBy(static::$ALIAS . '.id', 'desc');
            return;
        }

        $col = $this->resolveColumn($orderBy);
        $direction = $orderDirection ?? 'asc';

        $query->orderBy($col, $direction);
    }

    protected function resolveColumn(string $field)
    {
        $def = static::getFieldDefs()[$field];

        if (isset($def['expr'])) {
            return DB::raw($def['expr']);
        }

        return "{$def['table']}.{$def['column']}";
    }

    protected function extractFieldsNeeded(
        ?array $filters = null,
        ?string $orderBy = null,
        ?array $context = []
    ): array {
        $fields = [];

        if ($filters) {
            foreach ($filters as $f) {
                $fields[] = $f['field'];
            }
        }

        foreach (array_keys($this->getSystemFilters($context)) as $field) {
            $fields[] = $field;
        }

        if ($orderBy) {
            $fields[] = $orderBy;
        }

        return $fields;
    }

    /**
     * Obtiene las definiciones de campos (con lazy loading)
     */
    protected static function getFieldDefs(): array
    {
        $class = static::class;
        
        if (!isset(self::$_initialized[$class])) {
            self::$_initialized[$class] = true;
        }
        
        return static::$FIELD_DEFS;
    }
}