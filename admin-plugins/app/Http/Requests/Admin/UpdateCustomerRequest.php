<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
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
            'name'              => 'required|string|max:255',
            'email'             => "required|string|email|max:255|unique:users,email,{$this->customer->id},id",
            'password'          => 'nullable|string|min:8',
            'agency'            => ['required',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', User::AGENCY);
                    $query->orWhere('role', User::ADMIN);
                }),
            ]
        ];
    }
}
