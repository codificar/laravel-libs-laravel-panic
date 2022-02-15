<?php

namespace Codificar\Panic\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PanicSettingSegupRequest extends FormRequest
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
            'security_provider_agency' => 'required|string',
            'segup_login' => 'required|string',
            'segup_password' => 'required|string',
            'segup_request_url' => 'required|string',
            'segup_verification_url' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'security_provider_agency' =>  'security_provider_agency is required and must be a string ',
            'segup_login' => 'segup_login is required and must be a string ',
            'segup_password' => ' segup_password is required and must be a string ',
            'segup_request_url' => 'segup_request_url is required and must be a string ',
            'segup_verification_url' => 'segup_verification_url is required and must be a string ',
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
