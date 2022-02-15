<?php

namespace Codificar\Panic\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PanicStoreRequest extends FormRequest
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
            'ledger_id' => 'required|integer',
            'request_id' => 'required|integer',
            'security_agency' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'ledger_id' => 'ledger_id is required and must be an integer',
            'request_id' => 'request_id is required and must be an integer'
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
