<?php

namespace App\Http\Requests\Admin;

use App\Rules\Rut;
use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
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
            'rut'              => ['required', new Rut],
            'state_id'         => 'required',
            'resolution_nro'   => ['required'],
            'resolution_date'  => ['required', 'date_format:Y-m-d'],
            'gr'               => ['required'],
            'economy_activity' => ['required', 'regex:/[0-9]/', 'size:6'],
            'phone'            => ['required'],
            'is_wordpress'     => ['required', 'in:1,0'],
            'ecomerce_url'     => ['nullable','regex:/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i'],
            'name'             => ['required'],
            'email'            => ['required', 'string', 'email', 'max:255'],
            'address'          => ['required'],
            "logo"             => 'image|mimes:jpeg,png,jpg,gif,svg|max:1024,dimensions:max_width=512,max_height=512',
            'type_document'    => ['required', 'in:invoice,ballot,exempt_invoice']
        ];
    }

    public function withValidator($validator)
    {
        if ($validator->fails()) {
            session()->flash('message', ['danger', $validator->messages()->first()]);
        }

    }
}
