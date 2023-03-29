<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserKeyRequest extends FormRequest
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
            'alegra_user'           => 'required|string|max:255',
            'alegra_token'          => 'required|string|max:255',
            'wc_consumer_key'       => 'required|string|max:255',
            'wc_consumer_secret'    => 'required|string|max:255',
        ];
    }
}
