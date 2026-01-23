<?php

namespace Modules\Auth\Services\Tokens\Middleware;

use Modules\Auth\Services\Tokens\Contracts\TypeClient;
use Illuminate\Http\Request;
use stdClass; 

class AccessToken implements TypeClient
{   
    public static function execute(Request $request, stdClass $claims): void
    {
        $attributes = [
            'sub' => $claims->sub,
            'dni' => $claims->dni, 
            'level' => $claims->level, 
            'office_ids' => $claims->office_ids, 
            'permissions' => $claims->permissions, 
            'type_client' => $claims->type_client
        ];

        $request->attributes->add($attributes);
    }
}