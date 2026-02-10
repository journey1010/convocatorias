<?php

namespace Modules\ProfessionalRecords\Requests;

use Modules\Auth\Shared\Requests\Template;

class UpdateCertificationRequest extends Template
{
    public function authorize(): bool
    {
        return $this->verifyPermission(['p.postulante']);
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer',
            'name' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . date('Y'),
            'hours' => 'required|integer|min:1|max:9999',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'El id es requerido',
            'id.integer' => 'El id debe ser un número entero',
            'name.required' => 'El nombre de la certificación es requerido',
            'name.max' => 'El nombre no debe exceder 255 caracteres',
            'year.required' => 'El año es requerido',
            'year.min' => 'El año debe ser mayor o igual a 1900',
            'year.max' => 'El año no puede ser futuro',
            'hours.required' => 'Las horas son requeridas',
            'hours.min' => 'Las horas deben ser al menos 1',
            'hours.max' => 'Las horas no deben exceder 9999',
            'file.mimes' => 'El archivo debe ser PDF, JPG, JPEG o PNG',
            'file.max' => 'El archivo no debe exceder 4 MB',
        ];
    }
}
