<?php

namespace Modules\JobVacancies\Applications;

use Modules\JobVacancies\Applications\Dtos\JobProfileSearchDto;
use Modules\JobVacancies\Repositories\JobProfileRepository;
use Modules\JobVacancies\Enums\VacancyStatus;
use Modules\JobVacancies\Services\JobVacancyFileStorageService

class PaginationProfilesCase
{
    public function __construct(
        private readonly JobProfileRepository $repository
    ) {}

    public function exec(JobProfileSearchDto $dto): array
    {
        $paginator = $this->repository->searchPaginated($dto);
        $paginator->through(function ($profile) {
            return [
                'id' => $profile->id,
                'title' => $profile->title,
                'salary' => 'S/. '.$profile->salary,
                'code_profile' => $profile->code_profile,
                'file' => $profile->file,
                'locale_name' => $profile->locale->name,
                'specialization_area_name' => $profile->specializationArea->name,
                'office_name' => $profile->office->name,
                'status_name' => VacancyStatus::from($profile->status)->name,
            ];
        });

        $items = ($paginator->items())->map(function ($profile) {
            return [
                'id' => $profile->id,
                'office_name' => $profile->office?->name,
                'locale_name' => $profile->locale?->name,
                'specialization_area_name' => $profile->specializationArea?->name,
                'status_name' => $profile->vacancy?->status?->name,
                'title' => $profile->title,
                'salary' => $profile->salary,
                'code_profile' => $profile->code_profile,
            ];
        })->toArray();

        return [
            'data' => $items,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total()
            ]
        ];
    }
}