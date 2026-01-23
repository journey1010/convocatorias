<?php

namespace Modules\User\Requests;

use Modules\Shared\Requests\Template;
use Illuminate\Validation\Validator;

class ListerUserRequest extends Template {
    
    public function authorize(): bool
    {
        return $this->verifyPermission($this->attributes->get('permissions'), 'user.list');
    }

    public function rules(): array
    {
        $allowed = 'full_name,dni,email,nickname,phone,status';

        return [
            'itemsPerPage' => 'required|integer|max:100',
            'page' => 'required|integer|min:1',

            'roles' => 'nullable|array',
            'roles.*' => 'integer',

            'permissions' => 'nullable|array',
            'permissions.*' => 'integer',

            'filters' => 'nullable|array',

            'filters.*.field' => "required|string|in:$allowed",
            'filters.*.operator' => 'required|string|in:=,LIKE,IN',
            'filters.*.value' => 'required',

            'office_ids' => 'nullable|array',
            'office_ids.*' => 'integer'
        ];
    }

    protected function after(): array
    {
        return [
            function (Validator $validator) {
                $data = $this->validated();
                $filters = $data['filters'] ?? [];

                foreach ($filters as $filter) {

                    $field = $filter['field'];
                    $value = $filter['value'];

                    if ($field === 'dni') {
                        if (!preg_match('/^[0-9]{8}$/', (string) $value)) {
                            $validator->errors()->add('filters', 'El DNI debe tener exactamente 8 dígitos.');
                        }
                    }

                    if ($field === 'email') {
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $validator->errors()->add('filters', 'El email no tiene un formato válido.');
                        }
                    }

                    if ($field === 'status') {
                        if (!in_array((string) $value, ['0', '1', '2'], true)) {
                            $validator->errors()->add('filters', 'El estado debe ser 0, 1 o 2.');
                        }
                    }
                }
            }
        ];
    }

}