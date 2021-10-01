<?php

namespace Codificar\Panic\Http\Requests;

use Codificar\Panic\Models\Panic;
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

    /**
     * This prepare for validation function is called before validation is performed, and checks if the security provider is set on the app settings.
     *  To include other api calls to third party security agencies include the value into the if block below
     * @return array $request
     */
    protected function prepareForValidation()
    {
        $security_agency = Panic::getDirectedToSegup();
        if ($security_agency->value == 'segup') {
            $this->merge([
                'security_agency' => 'segup',
                'ledger_id' => $this->ledger_id,
                'request_id' => $this->request_id,
            ]);
        } else if ($security_agency->value == false) {
            $this->merge([
                'ledger_id' => $this->ledger_id,
                'request_id' => $this->request_id,
            ]);
        }
    }
}
