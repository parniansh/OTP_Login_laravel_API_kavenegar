<?php

namespace App\Http\Requests;

use App\Helpers\SerializeValidationErrorResponseHelper;
use App\Http\Resources\ErrorResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class OtpLoginRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'phone' => ['required','min:11','max:11','regex:/^([0-9\s\-\+\(\)]*)$/'],
            'code' => 'required | min:6 | max:6'
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $error =  new ErrorResource((object)[
            'error' => __('validation.RequestValidation')               
        ]);
        throw new HttpResponseException(response()->json($error,422));
    }
}
