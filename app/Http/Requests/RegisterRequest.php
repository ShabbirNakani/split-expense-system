<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => ['required', 'regex:/^([a-zA-Z]+\s)*[a-zA-Z]+$/', 'max:255'],
            'email' => ['required', 'string', 'max:255', 'unique:users', $emailFormate],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'number' => ['required', 'min:8', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'unique:users'],
            'Profilepic' => ['file', 'mimes:jpg,png,jpeg', 'between:100,10240'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Provide a name',
            'name.regex' => 'invalid name format',
            'email.required' => 'Provide an email',
            'number.required' => 'A contact number is required',
            'email.regex' => 'This Domain name is invalid, please provide a valid Domain name',
            'Profilepic.between' => 'Profile Pic size must be more than 100kb or less then 10mb.'
        ];
    }
}
