<?php

namespace App\Http\Requests\Agency;

use App\Models\Plan;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
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
            'plan_name'     => 'required|string|max:200',
            'plan_price'    => 'required|numeric|min:1',
            'interval'      => [
                'required',
                Rule::in([Plan::MONTH, Plan::YEAR]),
            ],
            'description'   => 'required|string',
            'platform'      => [
                'required',
                Rule::in([
                    Plan::ALEGRA,
                    Plan::SIIGO,
                    Plan::FAC_CHILE,
                    Plan::FAC_PERU,
                    Plan::BEON,
                    Plan::ANALITYCS,
                    Plan::PAGUE_A_TIEMPO
                ]),
            ],
            'qty_documents' => 'sometimes|integer|min:1',
        ];
    }
}
