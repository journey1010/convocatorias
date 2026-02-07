<?php

namespace Modules\Accounts\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FileStorageService
{
    private const STORAGE_PATH = 'personal_data_certs';

    public string $user_id; 

    public function storeCertificate(UploadedFile $file, string $type): string
    {
        $datePath = now()->format('Y/m'); 
        $fullPath = self::STORAGE_PATH . '/' . $datePath;

        $fileName = $this->generateFileName($type);
        
        return Storage::disk('private')->putFileAs(
            $fullPath,
            $file,
            $fileName
        );
    }

    public function deleteCertificate(?string $path): void
    {
        if (!$path) {
            return;
        }

        try {
            Storage::disk('private')->delete($path);
        } catch (\Exception $e) {
            Log::warning("Error deleting certificate: {$path}", ['error' => $e->getMessage()]);
        }
    }

    public function updateCertificate(?string $oldPath, UploadedFile $newFile, string $type): string
    {
        $this->deleteCertificate($oldPath);
        return $this->storeCertificate($newFile, $type);
    }

    private function generateFileName(string $type): string
    {
        return "{$type}_" . "{$this->user_id}_" . time() . '_' . uniqid() . '.pdf';
    }
}
