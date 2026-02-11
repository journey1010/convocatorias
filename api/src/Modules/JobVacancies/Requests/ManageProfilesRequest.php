<?php

namespace Modules\JobVacancies\Requests;

use Modules\Auth\Shared\Requests\Template;

class ManageProfilesRequest extends Template
{
    public function authorize(): bool
    {
        // Solo administradores
        return $this->verifyPermission(['p.admin', 'p.rrhh']);
    }

    public function rules(): array
    {
        return [
            'job_vacancy_id' => 'required|integer|exists:job_vacancies,id',
            'profiles' => 'required|array|min:1',
            'profiles.*.id' => 'nullable|integer|exists:job_profiles,id',
            'profiles.*.title' => 'required|string|max:255',
            'profiles.*.salary' => 'required|string|max:10',
            'profiles.*.office_id' => 'required|integer|exists:offices,id',
            'profiles.*.code_profile' => 'nullable|string|max:255',
            'profiles.*.file' => 'nullable|file|mimes:pdf,doc,docx|max:4096',
        ];
    }

    public function messages(): array
    {
        return [
            'job_vacancy_id.required' => 'El ID de la convocatoria es requerido',
            'job_vacancy_id.exists' => 'La convocatoria no existe',
            'profiles.required' => 'Debe proporcionar al menos un perfil',
            'profiles.min' => 'Debe proporcionar al menos un perfil',
            'profiles.*.title.required' => 'El título del perfil es requerido',
            'profiles.*.salary.required' => 'El salario es requerido',
            'profiles.*.office_id.required' => 'La oficina es requerida',
            'profiles.*.office_id.exists' => 'La oficina especificada no existe',
            'profiles.*.file.mimes' => 'El archivo debe ser PDF, DOC o DOCX',
            'profiles.*.file.max' => 'El archivo no debe exceder 4 MB',
        ];
    }
}
