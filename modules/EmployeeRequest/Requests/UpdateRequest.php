<?php

namespace Modules\EmployeeRequest\Requests;

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
            'budgeted'  => 'required',
            'fiscal_year_id'=>'required|exists:lkup_fiscal_years,id',
            'employee_type_id'=>'required',
            'duty_station_id'=>'required|exists:lkup_districts,id',
            'activity_code_id'=>'required|exists:lkup_activity_codes,id',
            'account_code_id'=>'required|exists:lkup_account_codes,id',
            'donor_code_id'=>'nullable|exists:lkup_donor_codes,id',
            'position_title'=>'required',
            'position_level'=>'required',
            'replacement_for'=>'required',
            'required_date'=>'required|date',
            'work_load'=>'required|numeric|max:255',
            'duration'=>'required',
            'reason_for_request'=>'required',
            'employee_type_other'=>'required_if:employee_type_id,0',
            'education_required'=>'required|string|max:191',
            'education_preferred'=>'nullable|string|max:191',
            'experience_required'=>'required|string|max:191',
            'experience_preferred'=>'nullable|string|max:191',
            'skills_required'=>'required|string|max:191',
            'skills_preferred'=>'nullable|string|max:191',
            'other_required'=>'nullable|string|max:191',
            'other_preferred'=>'nullable|string|max:191',
            'logistics_requirement'=>'required|string|max:191',
            'tor_jd_submitted'=>'nullable',
            'tentative_submission_date'=>'required_if:tor_jd_submitted,0',
            'attachment'=>'mimes:jpeg,png,pdf,docx|max:2048',
            'reviewer_id'=>'nullable',
            'approver_id'=>'required_if:btn,submit',
            'btn'=>'required',
        ];
    }

    public function messages()
    {
        return [
            'employee_type_other.required_if'=>'This field is required when employee type is other.',
            'tentative_submission_date.required_if'=>'This field is required when tor/jd is not submitted.',
            // 'attachment.required_if'=>'This field is required when tor/jd is submitted.',
            'approver_id.required_if'=>'The approver is required when submitted',
        ];
    }
}
