<?php

namespace Modules\TransportationBill\Requests;

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
            // 'bill_number'=>'required',
            'bill_date'=>'required|date',
            'shipper_name'=>'required',
            'shipper_address'=>'required',
            'consignee_name'=>'required',
            'consignee_address'=>'required',
            'remarks'=>'nullable',
            'instruction'=>'nullable',
            'approver_id'=>'required_if:btn,submit',
            'reviewer_id'=>'required_if:btn,submit',
            'btn'=>'required',
        ];
    }

    public function messages()
    {
        return [
            'approver_id.required_if'=>"The approver is required when submitted"
        ];
    }
}
