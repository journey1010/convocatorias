<?php

namespace Modules\ProfessionalRecords\Requests;

use Illuminate\Validation\Rule;
use Modules\Auth\Shared\Requests\Template;
use Modules\ProfessionalRecords\Enums\AcademicLevel;
use Modules\ProfessionalRecords\Enums\AcademicStatus;

class CreateAcademicRecordRequest extends Template
{
    protected $stopOnFirstFailure = true;
    
    public function authorize(): bool
    {
        return $this->verifyPermission(['p.postulante']);
    }

    public function rules(): array
    {
        return [
            'level' => ['required', Rule::enum(AcademicLevel::class)],
            'specialization_area_id' => [
                Rule::requiredIf(fn () => $this->input('level') > AcademicLevel::LEVEL_PRIMARY->value),
                'nullable',
                'integer',
                'exists:specialization_areas,id',
            ],
            'status' => ['required', Rule::enum(AcademicStatus::class)],
            'start_date' => 'required|date|before_or_equal:today|date_format:Y-m-d',
            'end_date' => 'nullable|date|after:start_date|date_format:Y-m-d',
            'description' => 'nullable|string|max:255',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:4096',
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
            'file.required' => 'El archivo es requerido',
            'file.mimes' => 'El archivo debe ser PDF, JPG, JPEG o PNG',
            'file.max' => 'El archivo no debe exceder 4 MB',
        ];
    }
}