<?php

namespace Modules\Auth\Services\Tokens\Middleware;

use Modules\Auth\Services\Tokens\Contracts\TypeClient;
use Illuminate\Http\Request;
use stdClass;

class RefreshToken implements TypeClient
{
    public static function execute(Request $request, stdClass $claims)
    {
        $attributes = [
            'sub' => $claims->sub,
            'exp' => $claims->exp,
        ];

        $request->attributes->add($attributes);
    }
}