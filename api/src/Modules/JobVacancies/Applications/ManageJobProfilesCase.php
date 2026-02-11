<?php

namespace Modules\JobVacancies\Applications;

use Illuminate\Support\Facades\DB;
use Modules\JobVacancies\Repositories\{JobVacancyRepository, JobProfileRepository};
use Modules\JobVacancies\Services\{JobVacancyFileStorageService, JobVacancyEditLogService};
use Modules\Auth\Infrastructure\Context\RequestContext;
use Infrastructure\Exceptions\JsonResponseException;

class ManageJobProfilesCase
{
    public function __construct(
        private JobVacancyRepository $vacancyRepository,
        private JobProfileRepository $profileRepository,
        private JobVacancyFileStorageService $fileService,
        private JobVacancyEditLogService $logService
    ) {}

    /**
     * Crea o actualiza los perfiles de una convocatoria
     */
    public function exec(int $vacancyId, array $profilesData, RequestContext $ctx): array
    {
        return DB::transaction(function () use ($vacancyId, $profilesData, $ctx) {
            $vacancy = $this->vacancyRepository->findByIdOrFail($vacancyId);

            // Verificar permisos
            if (!$this->vacancyRepository->canUserEdit($vacancy, $ctx)) {
                throw new JsonResponseException('No tienes permiso para editar perfiles de esta convocatoria', 403);
            }

            // Verificar si se pueden actualizar perfiles
            if (!$vacancy->canUpdateProfiles()) {
                throw new JsonResponseException(
                    'No se pueden editar perfiles cuando la convocatoria está en estado ' . $vacancy->status->name,
                    422
                );
            }

            $this->fileService->user_id = (string) $ctx->userId;
            $createdProfiles = [];

            foreach ($profilesData as $profileData) {
                $data = [
                    'locale_id' => $vacancy->locale_id,
                    'user_id' => $ctx->userId,
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

                if (isset($profileData['id']) && $profileData['id']) {
                    // Actualizar perfil existente
                    $profile = $this->profileRepository->findByIdOrFail($profileData['id']);
                    $profile = $this->profileRepository->update($profile, $data);
                } else {
                    // Crear nuevo perfil
                    $profile = $this->profileRepository->create($data);
                }

                $createdProfiles[] = [
                    'id' => $profile->id,
                    'title' => $profile->title,
                    'salary' => $profile->salary,
                    'office_id' => $profile->office_id,
                    'code_profile' => $profile->code_profile,
                    'file' => $profile->file,
                ];
            }

            // Registrar en el log
            if ($vacancy->shouldLogChanges()) {
                $this->logService->logChange(
                    $vacancy,
                    [],
                    ['profiles_updated' => count($createdProfiles)],
                    'profiles_updated',
                    $ctx
                );
            }

            return $createdProfiles;
        });
    }
}
