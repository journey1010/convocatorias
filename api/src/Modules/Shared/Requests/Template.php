<?php

namespace Modules\Shared\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Modules\Rbac\Services\AccessControlServices;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

class Template extends FormRequest
{
    use AccessControlServices;

    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        $messages = implode(' ', array_merge(...array_values($validator->errors()->getMessages())));
        
        $jsonResponse = new JsonResponse([
            'message' => $messages
        ], 422); 
        
        throw new HttpResponseException($jsonResponse);
    }

    public function rules(): array
    {
        return [];
    }

}