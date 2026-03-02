<?php

namespace Modules\JobVacancies\Requests;

use Modules\Auth\Shared\Requests\Template;
use Illuminate\Validation\Rule;
use Modules\JobVacancies\Enums\VacancyStatus;
use Modules\JobVacancies\Enums\VacancyMode;

class ListJobVacanciesRequest extends Template
{
    public function authorize(): bool
    {
        // Público - cualquiera puede listar
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => 'required|integer|min:1',
            'itemsPerPage' => 'required|integer|min:1|max:100',
            'status' => ['nullable', Rule::enum(VacancyStatus::class)],
        ];
    }
}