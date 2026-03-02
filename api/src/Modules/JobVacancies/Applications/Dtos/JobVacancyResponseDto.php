<?php

namespace Modules\JobVacancies\Applications\Dtos;

use Modules\JobVacancies\Enums\VacancyStatus;
use Modules\JobVacancies\Models\JobVacancy;
use Modules\JobVacancies\Services\JobVacancyFileStorageService as Storage;
use JsonSerializable;

class JobVacancyResponseDto implements JsonSerializable
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly VacancyStatus $status,
        public readonly bool $mode,
        public readonly string $start_date,
        public readonly string $close_date,
        public readonly array $files = [],
        public readonly array $profiles = [],
    ) {}

    public static function fromModel(JobVacancy $vacancy, bool $includeRelations = true): self
    {
        $files = [];
        $profiles = [];

        if ($includeRelations) {
            $files = $vacancy->files->map(fn($file) => [
                'id' => $file->id,
                'name' => $file->name,
                'file' => Storage::url($file->file)
            ])->toArray();

            $profiles = $vacancy->profiles->map(fn($profile) => [
                'id' => $profile->id,
                'title' => $profile->title,
                'salary' => $profile->salary,
                'office_id' => $profile->office_id,
                'code_profile' => $profile->code_profile,
                'file' => Storage::url($profile->file)
            ])->toArray();
        }

        return new self(
            id: $vacancy->id,
            title: $vacancy->title,
            status: $vacancy->status,
            mode: $vacancy->mode,
            start_date: $vacancy->start_date,
            close_date: $vacancy->close_date,
            files: $files,
            profiles: $profiles,
        );
    }

    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this), fn($value) => $value !== null && $value !== []);
    }
}