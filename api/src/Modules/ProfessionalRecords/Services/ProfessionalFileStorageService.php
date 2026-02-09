<?php

namespace Modules\ProfessionalRecords\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProfessionalFileStorageService
{
    private const STORAGE_PATH = 'professional_records';

    public string $user_id;

    public function storeFile(UploadedFile $file, string $type): string
    {
        $datePath = now()->format('Y/m');
        $fullPath = self::STORAGE_PATH . '/' . $datePath;

        $fileName = $this->generateFileName($type, $file->getClientOriginalExtension());

        return Storage::disk('private')->putFileAs(
            $fullPath,
            $file,
            $fileName
        );
    }

    public function deleteFile(?string $path): void
    {
        if (!$path) {
            return;
        }

        try {
            Storage::disk('private')->delete($path);
        } catch (\Exception $e) {
            Log::warning("Error deleting file: {$path}", ['error' => $e->getMessage()]);
        }
    }

    public function updateFile(?string $oldPath, UploadedFile $newFile, string $type): string
    {
        $this->deleteFile($oldPath);
        return $this->storeFile($newFile, $type);
    }

    private function generateFileName(string $type, string $extension): string
    {
        return "{$type}_" . "{$this->user_id}_" . time() . '_' . uniqid() . '.' . $extension;
    }
}
