<?php

namespace App\Http\Requests;

use App\Rules\checExpensesBeforeEdit;
use App\Rules\checExpensesBeforeEditRule;
use Illuminate\Foundation\Http\FormRequest;

class EditGroupRequest extends FormRequest
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
            'title' => ['required', 'max:255'],
            'discription' => ['required'],
            'users' => ['required', new checExpensesBeforeEditRule],
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Enter title to continue',
            'discription.required' => 'Provide discription to continue',
            'users.required' => 'At least one user is required to create a Group',
            'users.checExpensesBeforeEdit' => 'User can not be removed there are remainig Settelements'
        ];
    }
}
