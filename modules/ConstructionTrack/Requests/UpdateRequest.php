<?php

namespace Modules\ConstructionTrack\Requests;

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
            'signed_date'=>'required|date',
            'health_facility_name'=>'required',
            'facility_type'=>'required',
            'effective_date_from'=>'required|date',
            'type_of_work'=>'required',
            'district_id'=>'nullable|exists:lkup_districts,id',
            'province_id'=>'nullable|exists:lkup_provinces,id',
            'local_level_id'=>'nullable|exists:lkup_local_levels,id',
            'effective_date_to'=>'nullable|date',
            'office_id' => 'nullable',
            // 'approver_id'=>'required_if:btn,submit',
            'ohw_contribution'=>'required',
            'engineer_id'=>'required',
            'total_contribution_amount'=>'nullable',
            'total_contribution_percentage'=>'nullable',
            'work_start_date'=>'nullable|date',
            'work_completion_date'=>'nullable|date',
            'donor_codes'=>'nullable|array',
            'donor'=>'nullable',
            'metal_plaque_text'=>'nullable',
            'approval'=>'nullable',
            'btn'=>'required',
        ];
    }

    public function messages()
    {
        return [
            'approver_id.required_if'=>"The approver is required when submitted"
        ];
    }

    public function validated($key = null, $default = null)
    {
        $validated = $this->validator->validated();
        return data_get($validated, $key, $default);
    }
}
