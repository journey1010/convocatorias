<?php

namespace Modules\Rbac\Controllers;

use Modules\Shared\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Rbac\Models\{Permission, Role};
use Modules\Shared\Applications\Dtos\{
    PaginateGenericDTO,
    CollectionGenericDTO
};
use Modules\Rbac\Requests\{
    AssignPermissionRequest, 
    CreateRoleRequest,
    ListerRbacRequest
};

class RbacController extends Controller
{
    public function listerRole(ListerRbacRequest $request): JsonResponse
    {
        $page    = $request->input('page');
        $perPage = $request->input('per_page', 15);

        $query = Role::with(['permissions:id,name,display_name'])
            ->select('id', 'name', 'display_name', 'description')
            ->where('name', '!=', 'sysadmin')
            ->orderBy('name');

        if ($page) {
            $results = $query->paginate($perPage, ['*'], 'page', $page);

            $items = $results->map(function ($role) {
                $role->permissions->each->makeHidden('pivot');
                return $role;
            });

            $dto = new PaginateGenericDTO($items, $results->total());

            return response()->json($dto);
        }

        $items = $query->get()->map(function ($role) {
            $role->permissions->each->makeHidden('pivot');
            return $role;
        });

        $dto = new CollectionGenericDTO($items);

        return response()->json($dto);
    }

    public function listerPermission(ListerRbacRequest $request)
    {
        $page    = $request->input('page');
        $perPage = $request->input('per_page', 15);

        $query = Permission::select('id', 'display_name', 'description')
            ->where('name', '!=', '*')
            ->orderBy('display_name');

        if ($page) {
            $results = $query->paginate($perPage, ['*'], 'page', $page);

            $items = $results->map(function ($permission) {
                return $permission;
            });

            $dto = new PaginateGenericDTO($items, $results->total());

            return response()->json($dto);
        }

        $items = $query->get()->map(function ($permission) {
            return $permission;
        });

        $dto = new CollectionGenericDTO($items);

        return response()->json($dto);
    }

    public function createRole(CreateRoleRequest $request): JsonResponse
    {
        Role::create([
            'name' => $request->input('name'), 
            'display_name' => $request->input('display_name'),
            'description' => $request->input('description')
        ]);
        return response()->json(['message' => 'Rol creado']);
    }

    public function syncPermissionRole(AssignPermissionRequest $request): JsonResponse
    {
        $role = Role::find($request->input('id'));
        $role->syncPermissions($request->input('permission'));
        //Clean cache of permissions from own driver
        \Illuminate\Support\Facades\Cache::flush();
        return response()->json(['message' => 'Permiso sincronizado']);
    }
}