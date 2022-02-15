<?php

namespace Codificar\Panic\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PanicSettingStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'panic_button_enabled_user' => 'required|string',
            'panic_button_enabled_provider' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'panic_button_enabled_user' => 'panic_button_enabled_user is required and must be a string containing a boolean',
            'panic_button_enabled_provider' => 'panic_button_enabled_provider is required and must be a string containing a boolean',
        ];
    }
    /**
     * It Returns a json if the validation fails */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $validator->errors()->all(),
            'error_code' => 400,
        ]));
    }
}
