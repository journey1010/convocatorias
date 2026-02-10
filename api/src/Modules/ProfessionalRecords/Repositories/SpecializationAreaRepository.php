<?php

namespace Modules\ProfessionalRecords\Repositories;

use Modules\ProfessionalRecords\Models\SpecializationArea;

class SpecializationAreaRepository
{
    public function findById(int $id): ?SpecializationArea
    {
        return SpecializationArea::find($id);
    }

    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return SpecializationArea::orderBy('name')->get();
    }

    public function create(array $data): SpecializationArea
    {
        return SpecializationArea::create($data);
    }

    public function update(SpecializationArea $area, array $data): SpecializationArea
    {
        $area->update($data);
        return $area;
    }

    public function delete(SpecializationArea $area): bool
    {
        return $area->delete();
    }
}