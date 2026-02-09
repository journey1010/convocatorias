<?php

namespace Modules\ProfessionalRecords\Applications;

use Modules\ProfessionalRecords\Repositories\SpecializationAreaRepository;

class ListSpecializationAreasCase
{
    public function __construct(private SpecializationAreaRepository $repository) {}

    public function exec(): \Illuminate\Support\Collection
    {
        $areas = $this->repository->getAll();

        return $areas->map(function ($area) {
            return [
                'id' => $area->id,
                'name' => $area->name,
            ];
        });
    }
}
