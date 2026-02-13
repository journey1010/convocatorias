<?php

namespace Modules\Accounts\Requests;

use Modules\Auth\Shared\Requests\Template;

class UpsertPersonalDataExtraRequest extends Template
{
    public function authorize(): bool
    {
        return $this->verifyPermission(['p.postulante']);
    }

    public function rules(): array
    {
        return [
            'department_id' => 'required|integer|exists:departments,id',
            'province_id' => 'required|integer|exists:provinces,id',
            'district_id' => 'required|integer|exists:districts,id',
            'ruc' => 'required|string|max:11',
            'address' => 'required|string|max:255',
            'birthday' => 'required|date|before:today',
            'gender' => 'required|integer|in:1,2,3',
            'have_cert_disability' => 'required|boolean',
            'file_cert_disability' => 'required_if:have_cert_disability,true|file|mimes:pdf|max:4096',
            'have_cert_army' => 'required|boolean',
            'file_cert_army' => 'required_if:have_cert_army,true|file|mimes:pdf|max:4096',
            'have_cert_professional_credentials' => 'required|boolean',
            'file_cert_professional_credentials' => 'required_if:have_cert_professional_credentials,true|file|mimes:pdf|max:4096',
            'is_active_cert_professional_credentials' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'department_id.required' => 'El departamento es requerido',
            'department_id.exists' => 'El departamento seleccionado no existe',
            'province_id.required' => 'La provincia es requerida',
            'province_id.exists' => 'La provincia seleccionada no existe',
            'district_id.required' => 'El distrito es requerido',
            'district_id.exists' => 'El distrito seleccionado no existe',
            'ruc.required' => 'El RUC es requerido',
            'ruc.max' => 'El RUC no debe exceder 11 caracteres',
            'address.required' => 'La dirección es requerida',
            'address.max' => 'La dirección no debe exceder 255 caracteres',
            'birthday.required' => 'La fecha de nacimiento es requerida',
            'birthday.date' => 'La fecha de nacimiento debe ser una fecha válida',
            'birthday.before' => 'La fecha de nacimiento debe ser anterior a hoy',
            'gender.required' => 'El género es requerido',
            'gender.in' => 'El género debe ser 1, 2 o 3',
            'file_cert_disability.required_if' => 'El certificado de discapacidad es requerido',
            'file_cert_disability.mimes' => 'El certificado de discapacidad debe ser un PDF',
            'file_cert_disability.max' => 'El certificado de discapacidad no debe exceder 4 MB',
            'file_cert_army.required_if' => 'El certificado militar es requerido',
            'file_cert_army.mimes' => 'El certificado militar debe ser un PDF',
            'file_cert_army.max' => 'El certificado militar no debe exceder 4 MB',
            'file_cert_professional_credentials.required_if' => 'El certificado de credenciales profesionales es requerido',
            'file_cert_professional_credentials.mimes' => 'El certificado de credenciales profesionales debe ser un PDF',
            'file_cert_professional_credentials.max' => 'El certificado de credenciales profesionales no debe exceder 4 MB',
        ];
    }
}
