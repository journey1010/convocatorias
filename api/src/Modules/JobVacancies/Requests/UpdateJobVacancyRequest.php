<?php

namespace Modules\JobVacancies\Requests;

use Illuminate\Validation\Rule;
use Modules\Auth\Shared\Requests\Template;
use Modules\JobVacancies\Enums\VacancyStatus;

class UpdateJobVacancyRequest extends Template
{
    public function authorize(): bool
    {
        // Solo administradores pueden actualizar convocatorias
        return $this->verifyPermission(['p.admin', 'p.rrhh']);
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:job_vacancies,id',
            'title' => 'nullable|string|max:400',
            'status' => ['nullable', Rule::enum(VacancyStatus::class)],
            'mode' => 'nullable|boolean',
            'start_date' => 'nullable|date',
            'close_date' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'El ID de la convocatoria es requerido',
            'id.exists' => 'La convocatoria no existe',
            'title.max' => 'El título no debe exceder 400 caracteres',
            'start_date.date' => 'La fecha de inicio debe ser una fecha válida',
            'close_date.date' => 'La fecha de cierre debe ser una fecha válida',
            'mode.boolean' => 'El modo debe ser verdadero o falso',
        ];
    }
}
