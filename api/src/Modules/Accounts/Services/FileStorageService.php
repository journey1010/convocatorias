<?php

namespace Modules\Accounts\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Infrastructure\Exceptions\JsonResponseException;

class FileStorageService
{
    private const ALLOWED_MIME = 'application/pdf';
    private const MAX_SIZE_MB = 4;
    private const STORAGE_PATH = 'personal_data_certs';

    public function storeCertificate(UploadedFile $file, string $type): string
    {
        $this->validateFile($file);

        $fileName = $this->generateFileName($type);
        $path = Storage::disk('private')->putFileAs(
            self::STORAGE_PATH,
            $file,
            $fileName
        );

        return $path;
    }

    public function deleteCertificate(?string $path): void
    {
        if (!$path) {
            return;
        }

        try {
            Storage::disk('private')->delete($path);
        } catch (\Exception $e) {
            // Log the error but don't fail the operation
            Log::warning("Error deleting certificate: {$path}", ['error' => $e->getMessage()]);
        }
    }

    public function updateCertificate(?string $oldPath, UploadedFile $newFile, string $type): string
    {
        $this->deleteCertificate($oldPath);
        return $this->storeCertificate($newFile, $type);
    }

    private function validateFile(UploadedFile $file): void
    {
        // Validate MIME type
        if ($file->getMimeType() !== self::ALLOWED_MIME) {
            throw new JsonResponseException('El archivo debe ser un PDF válido', 422);
        }

        // Validate size (convert MB to bytes)
        $maxSizeBytes = self::MAX_SIZE_MB * 1024 * 1024;
        if ($file->getSize() > $maxSizeBytes) {
            throw new JsonResponseException(
                "El archivo no debe exceder " . self::MAX_SIZE_MB . " MB",
                422
            );
        }
    }

    private function generateFileName(string $type): string
    {
        return "{$type}_" . time() . '_' . uniqid() . '.pdf';
    }
}
