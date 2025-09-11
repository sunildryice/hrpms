<?php

namespace Modules\PurchaseRequest\Requests;

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
            'required_date'=>'required|date',
            'purpose'=>'nullable',
            'delivery_instructions'=>'nullable',
            'procurement_officer' => 'nullable|array',
            'modification_remarks'=>'nullable',
            'reviewer_id'=>'required_if:btn,submit',
            'approver_id'=>'required_if:btn,submit',
            'btn'=>'required',
        ];
    }

    public function messages()
    {
        return [
            'reviewer_id.required_if'=>"The finance reviewer is required when submitted",
            'approver_id.required_if'=>"The approver is required when submitted",
        ];
    }

    public function attributes()
    {
        return [
            'attachment' => 'Attachment',
        ];
    }
}
