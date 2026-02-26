<?php

namespace Modules\JobVacancies\Repositories;

use Modules\JobVacancies\Models\JobVacancy;
use Modules\Auth\Infrastructure\Context\RequestContext;
use Infrastructure\Exceptions\JsonResponseException;

class JobVacancyRepository
{
    public function findById(int $id): ?JobVacancy
    {
        return JobVacancy::with(['files', 'profiles', 'locale'])->find($id);
    }

    public function findByIdOrFail(int $id): JobVacancy
    {
        $vacancy = $this->findById($id);

        if (!$vacancy) {
            throw new JsonResponseException('Convocatoria no encontrada', 404);
        }

        return $vacancy;
    }

    /**
     * Lista pública de convocatorias (solo publicadas y activas)
     */
    public function getPublicList(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = JobVacancy::query()
            ->with(['files', 'profiles.office'])
            ->whereIn('status', [1]); // Solo PUBLICADA

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Lista para administradores (filtrada por locale)
     */
    public function getAdminList(RequestContext $ctx, array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = JobVacancy::query()
            ->with(['files', 'profiles.office', 'user'])
            ->whereIn('locale_id', [$ctx->localeId]);

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function create(array $data): JobVacancy
    {
        return JobVacancy::create($data);
    }

    public function update(JobVacancy $vacancy, array $data): JobVacancy
    {
        $vacancy->update($data);
        return $vacancy->fresh(['files', 'profiles']);
    }

    /**
     * Verifica si un usuario puede editar la convocatoria
     */
    public function canUserEdit(JobVacancy $vacancy, RequestContext $ctx): bool
    {
        // Debe ser del mismo locale
        if (!in_array($vacancy->locale_id, [$ctx->localeId])) {
            return false;
        }

        // Debe ser el creador o tener permisos de administrador
        return $vacancy->user_id === $ctx->userId || $ctx->isEmployee();
    }
}
