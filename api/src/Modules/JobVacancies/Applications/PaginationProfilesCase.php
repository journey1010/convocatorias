<?php

namespace Modules\JobVacancies\Applications;

use Modules\JobVacancies\Applications\Dtos\JobProfileSearchDto;
use Modules\JobVacancies\Repositories\JobProfileRepository;
use Modules\JobVacancies\Enums\VacancyStatus;
use Modules\JobVacancies\Services\JobVacancyFileStorageService;
use Modules\Shared\Applications\Dtos\PaginateGenericDTO;

class PaginationProfilesCase
{
    public function __construct(
        private readonly JobProfileRepository $repository
    ) {}

    public function exec(JobProfileSearchDto $dto): PaginateGenericDTO
    {
        $paginator = $this->repository->searchPaginated($dto);
        $paginator->through(function ($profile) {
            return [
                'id' => $profile->id,
                'title' => $profile->title,
                'salary' => 'S/ '.$profile->salary,
                'code_profile' => $profile->code_profile,
                'file' => JobVacancyFileStorageService::url($profile->file),
                'locale_name' => $profile->locale_name,
                'specialization_area_name' => $profile->specialization_area_name,
                'office_name' => $profile->office_name,
                'status_name' => VacancyStatus::from($profile->status_name)->label(),
                'job_vacancy_id' => $profile->job_vacancy_id,
                'job_vacancy_title' => $profile->job_vacancy_title,
                'job_vacancy_start_date' => $profile->job_vacancy_start_date,
                'job_vacancy_close_date' => $profile->job_vacancy_close_date
            ];
        });

        return new PaginateGenericDTO(
            items: $paginator->items(),
            total: $paginator->total()
        );
    }
}