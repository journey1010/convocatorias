<?php

namespace Modules\JobVacancies\Requests;

use Modules\Auth\Shared\Requests\Template;

class AttachFileRequest extends Template
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
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'name' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'job_vacancy_id.required' => 'El ID de la convocatoria es requerido',
            'job_vacancy_id.exists' => 'La convocatoria no existe',
            'file.required' => 'El archivo es requerido',
            'file.mimes' => 'El archivo debe ser PDF, DOC, DOCX, JPG, JPEG o PNG',
            'file.max' => 'El archivo no debe exceder 10 MB',
            'name.required' => 'El nombre del archivo es requerido',
            'name.max' => 'El nombre no debe exceder 255 caracteres',
        ];
    }
}
