<?php

namespace Modules\JobVacancies\Applications\Dtos;

use Modules\JobVacancies\Models\JobVacancy;

class JobVacancyResponseDto
{
    public function __construct(
        public readonly int $id,
        public readonly int $user_id,
        public readonly int $locale_id,
        public readonly string $title,
        public readonly int $status,
        public readonly bool $mode,
        public readonly string $start_date,
        public readonly string $close_date,
        public readonly string $created_at,
        public readonly string $updated_at,
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
                'file' => $file->file,
                'created_at' => $file->created_at->toISOString(),
            ])->toArray();

            $profiles = $vacancy->profiles->map(fn($profile) => [
                'id' => $profile->id,
                'title' => $profile->title,
                'salary' => $profile->salary,
                'office_id' => $profile->office_id,
                'code_profile' => $profile->code_profile,
                'file' => $profile->file,
                'created_at' => $profile->created_at->toISOString(),
            ])->toArray();
        }

        return new self(
            id: $vacancy->id,
            user_id: $vacancy->user_id,
            locale_id: $vacancy->locale_id,
            title: $vacancy->title,
            status: $vacancy->status->value,
            mode: $vacancy->mode,
            start_date: $vacancy->start_date->toDateString(),
            close_date: $vacancy->close_date->toDateString(),
            created_at: $vacancy->created_at->toISOString(),
            updated_at: $vacancy->updated_at->toISOString(),
            files: $files,
            profiles: $profiles,
        );
    }
}
