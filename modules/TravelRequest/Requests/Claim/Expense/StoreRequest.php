<?php

namespace Modules\TravelRequest\Requests\Claim\Expense;

use Illuminate\Foundation\Http\FormRequest;

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
            'activity_code_id'=>'required|exists:lkup_activity_codes,id',
            'expense_date'=>'required|date',
            'expense_amount'=>'required|numeric|min:0.01',
            'invoice_bill_number'=> 'nullable',
            'expense_description'=>'required',
            'attachment'=>'nullable|mimes:png,jpg,pdf|max:5120',
            // 'donor_code_id'=>'required|exists:lkup_donor_codes,id',
            // 'office_id'=>'required|exists:lkup_offices,id',
        ];
    }

    public function messages()
    {
        return [
            'attachment.mimes'=>'Only png,jpg or pdf files are allowed.',
            'attachment.size'=>'Maximum allowed file size is 5MB.',
        ];
    }
}
