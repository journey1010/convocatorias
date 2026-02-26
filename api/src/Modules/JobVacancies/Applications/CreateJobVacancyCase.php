<?php

namespace Modules\JobVacancies\Applications;

use Illuminate\Support\Facades\DB;
use Modules\JobVacancies\Applications\Dtos\{CreateJobVacancyDto, JobVacancyResponseDto};
use Modules\JobVacancies\Repositories\{JobVacancyRepository, JobProfileRepository, JobVacancyFileRepository};
use Modules\JobVacancies\Services\JobVacancyFileStorageService;

class CreateJobVacancyCase
{
    public function __construct(
        private JobVacancyRepository $vacancyRepository,
        private JobProfileRepository $profileRepository,
        private JobVacancyFileStorageService $fileService,
        private JobVacancyFileRepository $baseFileRepository
    ) {}

    public function exec(CreateJobVacancyDto $dto): JobVacancyResponseDto
    {
        return DB::transaction(function () use ($dto) {

            //1. Crear la convocatoria
            $vacancy = $this->vacancyRepository->create([
                'created_by' => $dto->created_by,
                'locale_id' => $dto->locale_id,
                'title' => $dto->title,
                'status' => $dto->status,
                'mode' => $dto->mode,
                'start_date' => $dto->start_date,
                'close_date' => $dto->close_date,
            ]);

            // Crear los perfiles asociados
            if (!empty($dto->profiles)) {
                foreach ($dto->profiles as $profileData) {
                    $data = [
                        'created_by' => $dto->created_by,
                        'job_vacancy_id' => $vacancy->id,
                        'title' => $profileData['title'],
                        'salary' => $profileData['salary'],
                        'locale_id' => $profileData['locale_id'],
                        'office_id' => $profileData['office_id'],
                        'code_profile' => $profileData['code_profile'],
                        'file' => $this->fileService->storeProfileFile($profileData['file']),
                    ];

                    $this->profileRepository->create($data);
                }
            }
            
            $this->baseFileRepository->create([
                'job_vacancy_id' => $vacancy->id,
                'locale_id' => $dto->locale_id,
                'file' => $this->fileService->storeVacancyFile($dto->doc_base_file),
                'name' => $dto->doc_base_file->getClientOriginalName(),
            ]);

            // Recargar con relaciones
            $vacancy = $this->vacancyRepository->findById($vacancy->id);

            return JobVacancyResponseDto::fromModel($vacancy);
        });
    }
}