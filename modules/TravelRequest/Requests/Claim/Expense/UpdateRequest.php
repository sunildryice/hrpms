<?php

namespace Modules\TravelRequest\Requests\Claim\Expense;

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
            'activity_code_id'=>'required|exists:lkup_activity_codes,id',
            'donor_code_id'=>'required|exists:lkup_donor_codes,id',
            'expense_date'=>'required|date',
            'expense_amount'=>'required|numeric|min:0.01',
            'office_id'=>'required|exists:lkup_offices,id',
            'expense_description'=>'required',
            'attachment'=>'nullable|mimes:png,jpg,pdf|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'attachment.mimes'=>'Only png,jpg or pdf files are allowed.',
            'attachment.size'=>'Maximum allowed file size is 2MB.',
        ];
    }
}
