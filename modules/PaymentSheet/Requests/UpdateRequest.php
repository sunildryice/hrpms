<?php

namespace Modules\PaymentSheet\Requests;

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
            'district_id'           => 'nullable',
            'verifier_id'           => 'required_if:btn,submit',
            'approver_id'           => 'required_if:btn,submit',
            'purchase_order_ids'    => 'nullable',
            'purpose'               => 'nullable',
            'btn'                   => 'required',
        ];
    }

    public function messages()
    {
        return [
            'verifier_id.required_if'   => "The verifier is required when submitted.",
            'approver_id.required_if'   => "The approver is required when submitted.",
        ];
    }
}
