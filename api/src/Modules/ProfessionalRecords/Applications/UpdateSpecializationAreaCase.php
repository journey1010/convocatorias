<?php

namespace Modules\ProfessionalRecords\Applications;

use Modules\ProfessionalRecords\Applications\Dtos\SpecializationAreaResponseDto;
use Modules\ProfessionalRecords\Repositories\SpecializationAreaRepository;

class UpdateSpecializationAreaCase
{
    public function __construct(private SpecializationAreaRepository $repository) {}

    public function exec(int $id, string $name): SpecializationAreaResponseDto
    {
        $area = $this->repository->findById($id);

        if (!$area) {
            throw new \Infrastructure\Exceptions\JsonResponseException('Área de especialización no encontrada', 404);
        }

        $updated = $this->repository->update($area, ['name' => $name]);

        return SpecializationAreaResponseDto::fromModel($updated);
    }
}
