<?php

namespace Codificar\Panic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Codificar\Panic\Models\Panic;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PanicSeenFormRequest extends FormRequest
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
			'panic_id' => 'required|integer'
		];
	}

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
	protected function prepareForValidation()
	{
		$panic = Panic::find($this->panicId);
		$panicId = null;
		if($panic) {
			$panicId = $panic->id;
			$this->panic = $panic;
		}
		$this->merge([
			'panic_id' => $panicId
		]);
	}

	/**
     * Returns a json if validation fails
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
	 * 
     * @return Json {'success','errors','error_code'}
     *
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success'       => false,
                'errors'        => $validator->errors()->all(),
                'error_code'    => \ApiErrors::REQUEST_FAILED
            ])
        );
    }
}

