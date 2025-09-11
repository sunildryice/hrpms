<?php

namespace Modules\EmployeeExit\Requests\EmployeeExitPayable;

use Illuminate\Foundation\Http\FormRequest;

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
            // 'employee_id'=>'required|unique:employees',
            // 'employee_id'=>'required',
            'salary_date_from'=>'nullable',
            'salary_date_to'=>'nullable',
            'leave_balance'=>'required',
            'salary_amount'=>'required',
            'festival_bonus'=>'required',
            'festival_bonus_date_from'=>'nullable',
            'festival_bonus_date_to'=>'nullable',
            'gratuity_amount'=>'required',
            'other_amount'=>'required',
            'advance_amount'=>'required',
            'loan_amount'=>'required',
            'other_payable_amount'=>'required',
            'deduction_amount'=>'nullable',
            'remarks'=>'nullable',
            'approver_id'=>'required_if:btn,submit',
            'btn'=>'required',
        ];
    }

    public function withValidator($validator)
    {
        // $validator->sometimes('festival_bonus_date_from', 'required', function($input) {
        //     return $input->festival_bonus > 0;
        // });
        // $validator->sometimes('festival_bonus_date_to', 'required', function($input) {
        //     return $input->festival_bonus > 0;
        // });
    }

}
