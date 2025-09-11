<?php

namespace Modules\EmployeeExit\Requests\EmployeeExitPayable;

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
            // 'employee_id'=>'required|unique:employees',
            'employee_id'=>'required',
            'salary_date_from'=>'required',
            'salary_date_to'=>'required',
            'leave_balance'=>'required',
            'salary_amount'=>'required',
            'festival_bonus'=>'required',
            'gratuity_amount'=>'required',
            'other_amount'=>'required',
            'advance_amount'=>'required',
            'loan_amount'=>'required',
            'other_payable_amount'=>'required',
            'deduction_amount'=>'nullable',
            'remarks'=>'nullable',
        ];
    }


}
