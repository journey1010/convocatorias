<?php

namespace Modules\JobVacancies\Repositories;

use Modules\JobVacancies\Models\JobVacancyFile;
use Infrastructure\Exceptions\JsonResponseException;

class JobVacancyFileRepository
{
    public function findById(int $id): ?JobVacancyFile
    {
        return JobVacancyFile::find($id);
    }

    public function findByIdOrFail(int $id): JobVacancyFile
    {
        $file = $this->findById($id);

        if (!$file) {
            throw new JsonResponseException('Archivo no encontrado', 404);
        }

        return $file;
    }

    public function getByVacancyId(int $vacancyId): \Illuminate\Database\Eloquent\Collection
    {
        return JobVacancyFile::where('job_vancancy_id', $vacancyId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create(array $data): JobVacancyFile
    {
        return JobVacancyFile::create($data);
    }

    public function update(JobVacancyFile $file, array $data): JobVacancyFile
    {
        $file->update($data);
        return $file;
    }

    public function delete(JobVacancyFile $file): bool
    {
        return $file->delete();
    }
}
