<?php

namespace Modules\Auth\Services\Tokens\Middleware;

use Modules\Auth\Services\Tokens\Contracts\TypeClient;
use Illuminate\Http\Request;
use stdClass; 

class AccessToken implements TypeClient
{   
    public static function execute(Request $request, stdClass $claims): void
    {
        $claimsArray = (array) $claims;

        $reserved = ['iss', 'iat', 'exp', 'nbf', 'jti'];
        
        $attributes = array_diff_key($claimsArray, array_flip($reserved));

        $request->attributes->add($attributes);
        
        if (!$request->attributes->has('office_ids')) {
             $request->attributes->set('office_ids', []);
        }
    }
}