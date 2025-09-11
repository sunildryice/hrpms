<?php

namespace Modules\Mfr\Requests\Agreement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'partner_organization_id' => 'required',
            'district_id' => 'required',
            'project_id' => 'required',
            'grant_number' => 'required|string',
            'effective_from' => 'required|date',
            'effective_to' => 'required|date',
            'approved_budget' => 'required|numeric',
            'opening_balance' => 'required|numeric|max:approved_budget',
            'opening_balance' => ['required', 'numeric', Rule::prohibitedIf($this->opening_balance > $this->approved_budget)],
            'opening_remarks' => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'opening_balance.prohibited' => 'Opening balance cannot be greater than the approved budget.'
        ];

    }
}
