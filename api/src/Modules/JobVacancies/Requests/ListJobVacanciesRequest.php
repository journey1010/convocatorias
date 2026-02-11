<?php

namespace Modules\JobVacancies\Requests;

use Modules\Auth\Shared\Requests\Template;

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
            // Filtros opcionales
            'status' => 'nullable|integer',
            'locale_id' => 'nullable|integer',
        ];
    }
}
