<?php

namespace Modules\Grn\Requests;

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
            'received_date'=>'required|date',
            'discount_amount'=>'nullable|numeric|min:0',
            'received_note'=>'nullable',
            'invoice_number'=>'nullable|string',
            'supplier_id'=>'nullable',
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
