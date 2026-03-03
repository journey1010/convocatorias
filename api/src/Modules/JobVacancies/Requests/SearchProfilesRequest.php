<?php

namespace Modules\JobVacancies\Requests;

use Modules\Auth\Shared\Requests\Template;
use Modules\JobVacancies\Enums\VacancyStatus;
use Illuminate\Validation\Rule;

class SearchProfilesRequest extends Template
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => 'required|integer|min:1',
            'per_page' => 'required|integer|min:1|max:100',
            'locale_id' => 'nullable|exists:locales,id',
            'office_id' => 'nullable|exists:offices,id',
            'specialization_area_id' => 'nullable|exists:specialization_areas,id',
            'status' => ['nullable', Rule::enum(VacancyStatus::class)],
            'salary' => 'nullable|numeric|min:0',
            'title' => 'nullable|string',
            'code_profile' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'page.required' => 'La página es requerida',
            'page.integer' => 'La página debe ser un número entero',
            'page.min' => 'La página debe ser mayor o igual a 1',
            'per_page.required' => 'La cantidad de resultados por página es requerida',
            'per_page.integer' => 'La cantidad de resultados por página debe ser un número entero',
            'per_page.min' => 'La cantidad de resultados por página debe ser mayor o igual a 1',
            'per_page.max' => 'La cantidad de resultados por página debe ser menor o igual a 100',
            'locale_id.exists' => 'El locale no existe',
            'office_id.exists' => 'La oficina no existe',
            'specialization_area_id.exists' => 'El área de especialización no existe',
            'status.enum' => 'El estado no es válido',
            'salary.numeric' => 'El salario debe ser un número',
            'salary.min' => 'El salario debe ser mayor o igual a 0',
            'title.string' => 'El título debe ser una cadena de texto',
            'code_profile.string' => 'El código del perfil debe ser una cadena de texto',
        ];
    }
}