<?php

namespace App\Http\Requests\Admin;

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
            'website'               => 'required|string|max:255',
            'alegra_user'           => 'nullable|string|max:255',
            'alegra_token'          => 'nullable|string|max:255',
            'wc_consumer_key'       => 'nullable|string|max:255',
            'wc_consumer_secret'    => 'nullable|string|max:255',
        ];
    }
}
