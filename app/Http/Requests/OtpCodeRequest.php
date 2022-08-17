<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Resources\ErrorResource;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class OtpCodeRequest extends FormRequest
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
