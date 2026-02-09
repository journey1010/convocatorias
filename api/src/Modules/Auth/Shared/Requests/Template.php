<?php

namespace Modules\Auth\Shared\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;

class Template extends BaseFormRequest
{
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