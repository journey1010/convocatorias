<?php

namespace Modules\Auth\Services\Tokens\Contracts;

use Illuminate\Http\Request;
use stdClass;

interface TypeClient
{
    public static function execute(Request $request, stdClass $claims);
}