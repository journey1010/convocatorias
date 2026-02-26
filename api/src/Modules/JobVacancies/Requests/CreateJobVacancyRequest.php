<?php

namespace Modules\JobVacancies\Requests;

use Modules\Auth\Shared\Requests\Template;

class CreateJobVacancyRequest extends Template
{
    public function authorize(): bool
    {
        return $this->verifyPermission('job.manage');
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:400',
            'mode' => 'required|boolean',
            'start_date' => 'required|date',
            'close_date' => 'required|date|after:start_date',
            'profiles' => 'nullable|array',
            'profiles.*.title' => 'required|string|max:255',
            'profiles.*.salary' => 'required|string|max:10',
            'profiles.*.office_id' => 'required|integer|exists:offices,id',
            'profiles.*.code_profile' => 'required|string|max:255',
            'profiles.*.locale_id' => 'required|integer|exists:locales,id', 
            'profiles.*.file' => 'required|file|mimes:pdf,doc,docx|max:4096',
            'doc_base_file' => 'required|file|mimes:pdf,doc,docx|max:4096',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El título de la convocatoria es requerido',
            'title.max' => 'El título no debe exceder 400 caracteres',
            'mode.required' => 'El modo de postulación es requerido',
            'mode.boolean' => 'El modo debe ser verdadero o falso',
            'start_date.required' => 'La fecha de inicio es requerida',
            'close_date.required' => 'La fecha de cierre es requerida',
            'close_date.after' => 'La fecha de cierre debe ser posterior a la fecha de inicio',
            'profiles.*.title.required' => 'El título del perfil es requerido',
            'profiles.*.salary.required' => 'El salario es requerido',
            'profiles.*.office_id.required' => 'La oficina es requerida',
            'profiles.*.office_id.exists' => 'La oficina especificada no existe',
            'profiles.*.file.mimes' => 'El archivo debe ser PDF, DOC o DOCX',
            'profiles.*.file.max' => 'El archivo no debe exceder 4 MB',
            'profiles.*.code_profile.required' => 'El código del perfil es requerido',
            'profiles.*.code_profile.max' => 'El código del perfil no debe exceder 255 caracteres',
            'profiles.*.locale_id.required' => 'El Local es requerido',
            'profiles.*.locale_id.exists' => 'El Local especificado no existe',
            'doc_base_file.mimes' => 'El archivo debe ser PDF, DOC o DOCX',
            'doc_base_file.max' => 'El archivo no debe exceder 4 MB'
        ];
    }
}