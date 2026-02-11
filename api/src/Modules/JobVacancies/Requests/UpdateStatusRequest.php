<?php

namespace Modules\JobVacancies\Requests;

use Illuminate\Validation\Rule;
use Modules\Auth\Shared\Requests\Template;
use Modules\JobVacancies\Enums\VacancyStatus;

class UpdateStatusRequest extends Template
{
    public function authorize(): bool
    {
        // Solo administradores
        return $this->verifyPermission(['p.admin', 'p.rrhh']);
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:job_vacancies,id',
            'status' => ['required', Rule::enum(VacancyStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'El ID de la convocatoria es requerido',
            'id.exists' => 'La convocatoria no existe',
            'status.required' => 'El estado es requerido',
        ];
    }
}
