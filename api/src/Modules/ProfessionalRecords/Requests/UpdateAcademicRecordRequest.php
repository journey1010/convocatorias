<?php

namespace Modules\ProfessionalRecords\Requests;

use Modules\Auth\Shared\Requests\Template;
use Modules\ProfessionalRecords\Models\AcademicRecord;

class UpdateAcademicRecordRequest extends Template
{
    public function authorize(): bool
    {
        return $this->verifyPermission(['p.postulante']);
    }

    public function rules(): array
    {
        return [
            'specialization_area_id' => 'required|integer|exists:specialization_areas,id',
            'level' => 'required|integer|in:' . implode(',', [
                AcademicRecord::LEVEL_PRIMARY,
                AcademicRecord::LEVEL_SECONDARY,
                AcademicRecord::LEVEL_TECHNICAL,
                AcademicRecord::LEVEL_UNIVERSITY,
                AcademicRecord::LEVEL_MASTER,
                AcademicRecord::LEVEL_DOCTORATE,
            ]),
            'status' => 'required|integer|in:' . implode(',', [
                AcademicRecord::STATUS_COMPLETED,
                AcademicRecord::STATUS_IN_PROGRESS,
                AcademicRecord::STATUS_INCOMPLETE,
            ]),
            'start_date' => 'required|date|before_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'description' => 'nullable|string|max:255',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ];
    }

    public function messages(): array
    {
        return [
            'specialization_area_id.required' => 'El área de especialización es requerida',
            'specialization_area_id.exists' => 'El área de especialización seleccionada no existe',
            'level.required' => 'El nivel académico es requerido',
            'level.in' => 'El nivel académico no es válido',
            'status.required' => 'El estado es requerido',
            'status.in' => 'El estado no es válido',
            'start_date.required' => 'La fecha de inicio es requerida',
            'start_date.date' => 'La fecha de inicio debe ser una fecha válida',
            'start_date.before_or_equal' => 'La fecha de inicio no puede ser futura',
            'end_date.date' => 'La fecha de fin debe ser una fecha válida',
            'end_date.after' => 'La fecha de fin debe ser posterior a la fecha de inicio',
            'description.max' => 'La descripción no debe exceder 255 caracteres',
            'file.mimes' => 'El archivo debe ser PDF, JPG, JPEG o PNG',
            'file.max' => 'El archivo no debe exceder 4 MB',
        ];
    }
}
