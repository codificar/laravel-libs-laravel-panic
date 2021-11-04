<?php

namespace Codificar\Panic\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PanicSettingAdminRequest extends FormRequest
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
            'panic_admin_email' => 'required|string',
            'panic_admin_phone_number' => 'required|string',
            'panic_admin_id' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'panic_admin_email' => 'panic_admin_email is required and must be a string containing a valid email',
            'panic_admin_phone_number' => 'panic_admin_phone_number is required and must be a string containing a valid phone number',
            'panic_admin_id' =>  'panic_admin_id is required and must be a string containing a integer',
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
