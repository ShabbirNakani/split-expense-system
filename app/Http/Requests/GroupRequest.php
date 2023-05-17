<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GroupRequest extends FormRequest
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
        return [
            'title' => 'required|max:255',
            'discription' => 'required',
            'users' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Enter title to continue',
            'discription.required' => 'Provide discription to continue',
            'users.required' => 'At least one user is required to create a Group'
        ];
    }
}
