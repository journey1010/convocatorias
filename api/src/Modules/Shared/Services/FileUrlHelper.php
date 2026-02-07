<?php

namespace Modules\Shared\Services;

class FileUrlHelper
{
    public static function getFileUrl(?string $endpoint, ?string $filePath): ?string
    {
        if (!$filePath) {
            return null;
        }

        $baseUrl = rtrim(config('app.url'), '/');

        $cleanEndpoint = trim($endpoint, '/');

        $cleanPath = ltrim($filePath, '/');

        return "{$baseUrl}/{$cleanEndpoint}/{$cleanPath}";
    }
}