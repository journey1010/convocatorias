<?php

namespace Modules\Auth\Services\Tokens\Enum;

enum TokenType: string 
{
    case EXTERNAL = 'external_app';
    case INTERNAL = 'internal';
    case REFRESH = 'refresh';
}