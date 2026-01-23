<?php 

namespace Modules\User\Models\QueryServices;

use Illuminate\Support\Facades\DB;
use Modules\User\Models\Dtos\UserFiltersDTO;
use Modules\Shared\Services\DefinesQueryFields;
use Modules\Shared\Services\ListerBaseDinamicQueryBuilder;

class ListerUserQueryService extends ListerBaseDinamicQueryBuilder{
    
    use DefinesQueryFields;

    protected static string $TABLE = 'users';
    protected static string $ALIAS = 'u';
    protected static array $BASE_FIELDS = [
        'id'
    ];

    public function __construct()
    {
        if (!empty(static::$FIELD_DEFS)) {
            return;
        }

        static::$FIELD_DEFS = array_merge(
            // Campos simples de la tabla principal
            self::defineSimpleFields([
                'id',
                'name',
                'last_name', 
                'dni', 
                'email', 
                'nickname', 
                'phone', 
                'status'
            ]),

            // Expresiones SQL
            self::defineExpression('full_name', "CONCAT(u.name, ' ', u.last_name)"),
        );
        $this->bootCustomFilters();
    }

    public function bootCustomFilters()
    {
        static::$FIELD_DEFS['full_name']['filter'] = function($query, $value = null, $operator = '=') {
            $fullName = DB::raw("CONCAT(u.name, ' ', u.last_name)");
            $fullNameInverse = DB::raw("CONCAT(u.last_name, ' ', u.name)");
            $query->where(function ($q) use ($fullName, $fullNameInverse, $operator, $value) {
                $q->where($fullName, 'like', '%' . $value . '%')
                  ->orWhere($fullNameInverse, 'like', '%' . $value . '%');
            });
        };
    }

    protected function getSystemFilters(?array $context = null): array
    {
        return [
            'level' => function($query) use ($context){
                $query->where('u.level', '>=', $context['level']);
            }
        ];
    }
    
    public function paginate(UserFiltersDTO $dto): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = $this->buildQuery(
                select: $dto->select, 
                filters: $dto->userFilters, 
                context: $dto->context);

        $this->applyRelationFilters($query, $dto);

        return $query->paginate(page: $dto->page, perPage: $dto->perPage);
    }

    protected function applyRelationFilters($query, UserFiltersDTO $dto): void
    {
        if (!empty($dto->roleIds)) {
            $query->whereExists(function ($subquery) use ($dto) {
                $subquery->select(DB::raw(1))
                    ->from('role_user')
                    ->whereColumn('role_user.user_id', 'u.id')
                    ->whereIn('role_user.role_id', $dto->roleIds);
            });
        }

        if (!empty($dto->permissionIds)) {
            $query->whereExists(function ($subquery) use ($dto) {
                $subquery->select(DB::raw(1))
                    ->from('permission_user')
                    ->whereColumn('permission_user.user_id', 'u.id')
                    ->whereIn('permission_user.permission_id', $dto->permissionIds);
            });
        }

        if (!empty($dto->officeIds)) {
            $query->whereExists(function ($subquery) use ($dto) {
                $subquery->select(DB::raw(1))
                    ->from('office_users')
                    ->whereColumn('office_users.user_id', 'u.id')
                    ->whereIn('office_users.office_id', $dto->officeIds);
            });
        }
    }
}