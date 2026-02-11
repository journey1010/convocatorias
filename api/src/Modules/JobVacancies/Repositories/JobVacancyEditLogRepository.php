<?php

namespace Modules\JobVacancies\Repositories;

use Modules\JobVacancies\Models\JobVacancyEditLog;

class JobVacancyEditLogRepository
{
    public function getByVacancyId(int $vacancyId): \Illuminate\Database\Eloquent\Collection
    {
        return JobVacancyEditLog::where('job_vacancy_id', $vacancyId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create(array $data): JobVacancyEditLog
    {
        return JobVacancyEditLog::create($data);
    }
}
