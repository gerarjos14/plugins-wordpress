<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanRequest extends FormRequest
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
            'plan_price'    => 'required|numeric|min:1',
            'plan_name'     => [
                Rule::requiredIf(function () {
                    return $this->plan->interval === \App\Models\Plan::LIFETIME;
                }), 'string', 'max:255'
            ],
            'description'   => [
                Rule::requiredIf(function () {
                    return $this->plan->interval === \App\Models\Plan::LIFETIME;
                }), 'string', 'max:255'
            ],
            'qty_documents' => 'sometimes|integer|min:1',
        ];
    }
}
