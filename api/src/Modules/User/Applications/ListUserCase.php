<?php

namespace Modules\User\Applications;

use Illuminate\Http\Request;
use Modules\User\Models\Dtos\UserFiltersDTO;
use Modules\Shared\Applications\Dtos\PaginateGenericDTO;
use Modules\User\Models\User;

class ListUserCase {

    public function exec(Request $request): PaginateGenericDTO
    {
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
            'sub' => $request->attributes->get('sub'),
            'level' => $request->attributes->get('level'), 
            'office_id' => $request->attributes->get('office_ids'), 
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