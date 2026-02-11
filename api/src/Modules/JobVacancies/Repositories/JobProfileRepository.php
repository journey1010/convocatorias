<?php

namespace Modules\JobVacancies\Repositories;

use Modules\JobVacancies\Models\JobProfile;
use Infrastructure\Exceptions\JsonResponseException;

class JobProfileRepository
{
    public function findById(int $id): ?JobProfile
    {
        return JobProfile::find($id);
    }

    public function findByIdOrFail(int $id): JobProfile
    {
        $profile = $this->findById($id);

        if (!$profile) {
            throw new JsonResponseException('Perfil no encontrado', 404);
        }

        return $profile;
    }

    public function getByVacancyId(int $vacancyId): \Illuminate\Database\Eloquent\Collection
    {
        return JobProfile::where('job_vacancy_id', $vacancyId)
            ->with('office')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function create(array $data): JobProfile
    {
        return JobProfile::create($data);
    }

    public function update(JobProfile $profile, array $data): JobProfile
    {
        $profile->update($data);
        return $profile;
    }

    public function delete(JobProfile $profile): bool
    {
        return $profile->delete();
    }

    /**
     * Elimina todos los perfiles de una convocatoria
     */
    public function deleteByVacancyId(int $vacancyId): void
    {
        JobProfile::where('job_vacancy_id', $vacancyId)->delete();
    }
}
