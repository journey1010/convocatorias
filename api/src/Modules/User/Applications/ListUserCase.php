<?php

namespace Modules\User\Applications;

use Illuminate\Http\Request;
use Modules\Shared\Applications\Dtos\PaginateGenericDTO;
use Modules\User\Models\User;
use Modules\Auth\Infrastructure\Context\RequestContextResolver;

class ListUserCase
{
    public function exec(Request $request): PaginateGenericDTO
    {
        $ctx = RequestContextResolver::fromRequest($request);
        
        $query = User::where('level', '>=', $ctx->level); // Solo usuarios del mismo nivel o inferior

        // Restricción de oficinas
        $permissions = $request->permission_users ?? [];
        if (!in_array('*', $permissions)) {
            // Si no tiene permiso wildcard, solo ver usuarios de sus oficinas
            $query->whereHas('officeUser', fn($q) => 
                $q->whereIn('office_id', $ctx->officeIds ?? [])
            );
        }

        // Aplicar búsqueda
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                  ->orWhere('last_name', 'LIKE', "%$search%")
                  ->orWhere('dni', 'LIKE', "%$search%")
                  ->orWhere('email', 'LIKE', "%$search%")
                  ->orWhere('phone', 'LIKE', "%$search%")
                  ->orWhere('nickname', 'LIKE', "%$search%");
            });
        }

        // Filtro por oficina específica
        if ($request->has('office_id')) {
            $query->whereHas('officeUser', fn($q) => 
                $q->where('office_id', $request->input('office_id'))
            );
        }

        $result = $query->paginate(
            $request->input('itemsPerPage', 15),
            ['id', 'name', 'last_name', 'dni', 'email', 'phone', 'nickname', 'status', 'level'],
            'page',
            $request->input('page', 1)
        );

        return new PaginateGenericDTO(
            $result->items(),
            $result->total()
        );
    }
}
