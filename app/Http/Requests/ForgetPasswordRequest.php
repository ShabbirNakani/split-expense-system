<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $emailFormate = 'regex:/(.*)@(gmail|hotmail|webcodegenie|mailinator|yahoo)\.(com|ac.in|gov.in|net)/i';
        return [
            'email' => ['required', 'string', 'max:255', 'unique:users', $emailFormate],
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Provide an email',
            'email.regex' => 'This Domain name is invalid, please provide a valid Domain name',
        ];
    }
}
