<?php

namespace Modules\JobVacancies\Applications;

use Modules\JobVacancies\Applications\Dtos\JobVacancyResponseDto;
use Modules\JobVacancies\Repositories\JobVacancyRepository;
use Modules\Auth\Infrastructure\Context\RequestContext;

class GetJobVacancyDetailsCase
{
    public function __construct(
        private JobVacancyRepository $repository
    ) {}

    public function exec(int $id, ?RequestContext $ctx): JobVacancyResponseDto
    {
        $vacancy = $this->repository->findByIdOrFail($id);

        return JobVacancyResponseDto::fromModel($vacancy, true);
    }
}
