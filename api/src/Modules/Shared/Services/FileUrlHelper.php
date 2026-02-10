<?php

namespace Modules\Shared\Services;

class FileUrlHelper
{
    public static function getFileUrl(string $routeName, string $wildcard, ?string $filePath): ?string
    {
        if (!$filePath) {
            return null;
        }

        $baseUrl = rtrim(config('app.url'), '/');
        $relativeRoute = route($routeName, [$wildcard => $filePath], false);

        return $baseUrl . $relativeRoute;
    }
}