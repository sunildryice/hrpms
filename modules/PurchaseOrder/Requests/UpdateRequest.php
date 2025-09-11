<?php

namespace Modules\PurchaseOrder\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
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
            'district_ids'=>'required|array',
            'supplier_id'=>'required|exists:suppliers,id',
            'purpose'=>'nullable',
            'delivery_date'=>'required',
            'delivery_location' => 'nullable',
            'delivery_instructions' => 'nullable',
            'currency_id'=>'nullable',
            'reviewer_id'=>'required_if:btn,submit',
            'approver_id'=>'required_if:btn,submit',
            'btn'=>'required',
        ];
    }

    public function messages()
    {
        return [
            'reviewer_id.required_if'=>"The reviewer is required when submitted",
            'approver_id.required_if'=>"The approver is required when submitted",
        ];
    }
}
