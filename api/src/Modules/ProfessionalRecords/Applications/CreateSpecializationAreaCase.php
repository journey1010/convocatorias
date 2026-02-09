<?php

namespace Modules\ProfessionalRecords\Applications;

use Modules\ProfessionalRecords\Applications\Dtos\{CreateSpecializationAreaDto, SpecializationAreaResponseDto};
use Modules\ProfessionalRecords\Repositories\SpecializationAreaRepository;

class CreateSpecializationAreaCase
{
    public function __construct(private SpecializationAreaRepository $repository) {}

    public function exec(CreateSpecializationAreaDto $dto): SpecializationAreaResponseDto
    {
        $data = ['name' => $dto->name];
        $area = $this->repository->create($data);
        

        return SpecializationAreaResponseDto::fromModel($area);
    }
}
