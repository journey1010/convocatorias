<?php

namespace Modules\User\Applications;

use Illuminate\Http\Request;
use Modules\User\Models\Dtos\UserFiltersDTO;
use Modules\Shared\Applications\Dtos\PaginateGenericDTO;
use Modules\User\Models\User;
use Modules\Auth\Infrastructure\Context\RequestContextResolver;

class ListUserCase
{

    public function exec(Request $request): PaginateGenericDTO
    {
        $ctx = RequestContextResolver::fromRequest($request);

        $select = [
            'id',
            'full_name',
            'dni',
            'email',
            'nickname',
            'phone',
            'status'
        ];

        $context = [
            'permissions' => $request->permission_users,
            'sub' => $ctx->userId,
            'level' => $ctx->level,
            'office_id' => $ctx->officeIds,
        ];

        $result = User::paginate(new UserFiltersDTO(
            $request->input('page'),
            $request->input('itemsPerPage'),
            $select,
            $request->input('filters'),
            $request->input('roles'),
            $request->input('permissions'),
            $request->input('office_ids'),
            $context
        ));

        return new PaginateGenericDTO(
            $result->items(),
            $result->total()
        );
    }
}
