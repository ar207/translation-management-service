<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateLocaleRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|unique:locales,name,' . $this->locale,
            'short_code' => 'required|string|unique:locales,short_code,' . $this->locale,
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
