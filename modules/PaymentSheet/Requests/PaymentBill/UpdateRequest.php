<?php

namespace Modules\PaymentSheet\Requests\PaymentBill;

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
            'category_id'=>'required|exists:lkup_bill_categories,id',
            'supplier_id'=>'required|exists:suppliers,id',
            'bill_number'=>'required',
            'bill_date'=>'required|date',
            'bill_amount'=>'required|numeric|min:0.01',
            'remarks'=>'nullable',
            'attachment'=>'nullable|mimes:png,jpg,pdf|max:2048',
        ];
    }

    public function messages()
    {
        return [];
    }
}
