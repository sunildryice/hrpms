<?php

namespace Modules\DistributionRequest\Requests;

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
            'district_id' => 'nullable|exists:lkup_districts,id',
            'office_id' => 'nullable|exists:lkup_offices,id',
            'project_code_id' => 'nullable|exists:lkup_project_codes,id',
            'health_facility_id' => 'nullable|exists:lkup_health_facilities,id',
            // 'health_facility_name'=>'required',
            'remarks' => 'nullable',
            'approver_id' => 'required_if:btn,submit',
            'btn' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'approver_id.required_if' => "The approver is required when submitted",
        ];
    }
}
