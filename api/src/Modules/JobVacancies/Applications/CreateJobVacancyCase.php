<?php

namespace Modules\JobVacancies\Applications;

use Illuminate\Support\Facades\DB;
use Modules\JobVacancies\Applications\Dtos\{CreateJobVacancyDto, JobVacancyResponseDto};
use Modules\JobVacancies\Repositories\{JobVacancyRepository, JobProfileRepository};
use Modules\JobVacancies\Services\JobVacancyFileStorageService;
use Infrastructure\Exceptions\JsonResponseException;

class CreateJobVacancyCase
{
    public function __construct(
        private JobVacancyRepository $vacancyRepository,
        private JobProfileRepository $profileRepository,
        private JobVacancyFileStorageService $fileService
    ) {}

    public function exec(CreateJobVacancyDto $dto): JobVacancyResponseDto
    {
        return DB::transaction(function () use ($dto) {
            $this->fileService->user_id = (string) $dto->created_by;

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
                        'locale_id' => $dto->locale_id,
                        'created_by' => $dto->created_by,
                        'job_vacancy_id' => $vacancy->id,
                        'title' => $profileData['title'],
                        'salary' => $profileData['salary'],
                        'office_id' => $profileData['office_id'],
                        'code_profile' => $profileData['code_profile'] ?? null,
                        'file' => '',
                    ];

                    // Almacenar archivo de perfil si existe
                    if (isset($profileData['file']) && $profileData['file']) {
                        $data['file'] = $this->fileService->storeProfileFile($profileData['file']);
                    }

                    $this->profileRepository->create($data);
                }
            }

            // Recargar con relaciones
            $vacancy = $this->vacancyRepository->findById($vacancy->id);

            return JobVacancyResponseDto::fromModel($vacancy);
        });
    }
}
