<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreLocaleRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|unique:locales,name',
            'short_code' => 'required|string|unique:locales,short_code',
        ];
    }

    public function authorize()
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = error_processor($validator);
        throw new HttpResponseException(response()->json([
            'errors' => $errors,
            'message' => 'Validation Error',
        ], 422));
    }
}
