<?php

namespace Modules\JobVacancies\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class JobVacancyFileStorageService
{
    public string $user_id;

    /**
     * Almacena un archivo de convocatoria
     */
    public function storeVacancyFile(UploadedFile $file, string $prefix = 'vacancy'): string
    {
        $path = "job_vacancies/{$this->user_id}/{$prefix}";
        $filename = time() . '_' . $file->getClientOriginalName();
        
        Storage::disk('public')->putFileAs($path, $file, $filename);
        
        return "{$path}/{$filename}";
    }

    /**
     * Almacena un archivo de perfil
     */
    public function storeProfileFile(UploadedFile $file): string
    {
        return $this->storeVacancyFile($file, 'profiles');
    }

    /**
     * Elimina un archivo
     */
    public function deleteFile(string $filePath): bool
    {
        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->delete($filePath);
        }
        
        return false;
    }
}