<?php

namespace Modules\ProfessionalRecords\Requests;

use Modules\Auth\Shared\Requests\Template;
use Modules\ProfessionalRecords\Models\JobRecord;

class CreateJobRecordRequest extends Template
{
    public function authorize(): bool
    {
        return $this->verifyPermission(['p.postulante']);
    }

    public function rules(): array
    {
        return [
            'entity_name' => 'required|string|max:255',
            'type' => 'required|integer|in:' . implode(',', [
                JobRecord::TYPE_PRIVATE,
                JobRecord::TYPE_PUBLIC,
            ]),
            'specialization_area' => 'required|string|max:255',
            'status' => 'required|integer|in:' . implode(',', [
                JobRecord::STATUS_IN_PROGRESS,
                JobRecord::STATUS_FINISHED,
            ]),
            'description' => 'nullable|string|max:500',
            'start_date' => 'required|date|before_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ];
    }

    public function messages(): array
    {
        return [
            'entity_name.required' => 'El nombre de la entidad es requerido',
            'entity_name.max' => 'El nombre de la entidad no debe exceder 255 caracteres',
            'type.required' => 'El tipo de entidad es requerido',
            'type.in' => 'El tipo debe ser privado o público',
            'specialization_area.required' => 'El área de especialización es requerida',
            'specialization_area.max' => 'El área de especialización no debe exceder 255 caracteres',
            'status.required' => 'El estado es requerido',
            'status.in' => 'El estado no es válido',
            'description.max' => 'La descripción no debe exceder 500 caracteres',
            'start_date.required' => 'La fecha de inicio es requerida',
            'start_date.before_or_equal' => 'La fecha de inicio no puede ser futura',
            'end_date.after' => 'La fecha de fin debe ser posterior a la fecha de inicio',
            'file.mimes' => 'El archivo debe ser PDF, JPG, JPEG o PNG',
            'file.max' => 'El archivo no debe exceder 4 MB',
        ];
    }
}
