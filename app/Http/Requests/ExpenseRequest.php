<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
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
            'amount' => 'required',
            'expenseDate' => 'required',
            'expenseUsers' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Enter title to continue',
            'amount.required' => 'Enter amount to continue',
            'expenseDate.required' => 'Enter expese date to continue',
            'expenseUsers.required' => 'Please select atleast one Friend'
        ];
    }
}


// rules: {
//     title: {
//         required: true,
//         // lettersonly: true
//     },
//     amount: {
//         required: true,
//         number: true
//     },
//     expenseDate: "required",
//     'expenseUsers[]': {
//         required: true,
//     },
// },
