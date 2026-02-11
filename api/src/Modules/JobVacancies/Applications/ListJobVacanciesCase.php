<?php

namespace Modules\JobVacancies\Applications;

use Modules\JobVacancies\Repositories\JobVacancyRepository;
use Modules\Auth\Infrastructure\Context\RequestContext;

class ListJobVacanciesCase
{
    public function __construct(
        private JobVacancyRepository $repository
    ) {}

    /**
     * Lista convocatorias - pública o para administradores
     */
    public function exec(?RequestContext $ctx, array $filters = []): array
    {
        // Si tiene contexto de autenticación y es empleado, mostrar lista de admin
        if ($ctx && $ctx->isEmployee()) {
            $vacancies = $this->repository->getAdminList($ctx, $filters);
        } else {
            // Lista pública
            $vacancies = $this->repository->getPublicList($filters);
        }

        return $vacancies->map(function ($vacancy) use ($ctx) {
            $data = [
                'id' => $vacancy->id,
                'title' => $vacancy->title,
                'status' => $vacancy->status->value,
                'mode' => $vacancy->mode,
                'start_date' => $vacancy->start_date->toDateString(),
                'close_date' => $vacancy->close_date->toDateString(),
                'created_at' => $vacancy->created_at->toISOString(),
                'profiles' => $vacancy->profiles->map(fn($p) => [
                    'id' => $p->id,
                    'title' => $p->title,
                    'salary' => $p->salary,
                    'office_id' => $p->office_id,
                    'code_profile' => $p->code_profile,
                ])->toArray(),
                'files' => $vacancy->files->map(fn($f) => [
                    'id' => $f->id,
                    'name' => $f->name,
                    'file' => $f->file,
                ])->toArray(),
            ];

            // Datos adicionales para administradores
            if ($ctx && $ctx->isEmployee()) {
                $data['user_id'] = $vacancy->user_id;
                $data['locale_id'] = $vacancy->locale_id;
            }

            return $data;
        })->toArray();
    }
}
