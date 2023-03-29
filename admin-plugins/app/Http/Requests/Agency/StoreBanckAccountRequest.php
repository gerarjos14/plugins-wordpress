<?php

namespace App\Http\Requests\Agency;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBanckAccountRequest extends FormRequest
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
            'name' => 'required',
            'last_name' => 'required',
            'account_number' => 'required',
            'account_type' => ['required',
                Rule::in([\App\Models\BankAccount::SAVINGS, \App\Models\BankAccount::CHECKING]),
            ],
            'bank_name' => 'required',
            'identity_card' => 'required',
        ];
    }
}
