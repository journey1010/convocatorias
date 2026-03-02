<?php

namespace Modules\JobVacancies\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\JobVacancies\Models\JobProfile;
use Infrastructure\Exceptions\JsonResponseException;
use Illuminate\Support\Facades\DB;

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

    public function searchPaginated(\Modules\JobVacancies\Applications\Dtos\JobProfileSearchDto $dto): LengthAwarePaginator
    {
        $query = DB::table('job_profiles')
            ->select(
                'job_profiles.id',
                'job_profiles.title',
                'job_profiles.salary',
                'job_profiles.code_profile',
                'job_profiles.file',
                'offices.name as office_name',
                'locales.name as locale_name',
                'specialization_areas.name as specialization_area_name',
                'job_vacancies.status as status_name',
                'job_vacancies.id as job_vacancy_id'
            )->join('offices', 'job_profiles.office_id', '=', 'offices.id')
            ->join('locales', 'job_profiles.locale_id', '=', 'locales.id')
            ->join('specialization_areas', 'job_profiles.specialization_area_id', '=', 'specialization_areas.id')
            ->join('job_vacancies', 'job_profiles.job_vacancy_id', '=', 'job_vacancies.id')
            ->when($dto->locale_id, function ($query) use ($dto) {
                $query->where('job_profiles.locale_id', $dto->locale_id);
            })
            ->when($dto->office_id, function ($query) use ($dto) {
                $query->where('job_profiles.office_id', $dto->office_id);
            })
            ->when($dto->specialization_area_id, function ($query) use ($dto) {
                $query->where('job_profiles.specialization_area_id', $dto->specialization_area_id);
            })
            ->when($dto->status, function ($query) use ($dto) {
                $query->where('job_vacancies.status', $dto->status);
            })
            ->when($dto->salary, function ($query) use ($dto) {
                $query->where('job_profiles.salary', '>=', $dto->salary);
            })
            ->when($dto->title, function ($query) use ($dto) {
                $query->where('job_profiles.title', 'like', '%' . $dto->title . '%');
            })
            ->when($dto->code_profile, function ($query) use ($dto) {
                $query->where('job_profiles.code_profile', 'like', '%' . $dto->code_profile . '%');
            });
            
        return $query->paginate($dto->per_page, ['*'], 'page', $dto->page);
    }
}