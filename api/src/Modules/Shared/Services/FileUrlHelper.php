<?php

namespace Modules\Shared\Services;

class FileUrlHelper
{
    public static function getFileUrl(string $routeName, string $wildcard, ?string $filePath): ?string
    {
        if (!$filePath) {
            return null;
        }        
        $route = route($routeName, [$wildcard => $filePath]);

        return $route;
    }
}