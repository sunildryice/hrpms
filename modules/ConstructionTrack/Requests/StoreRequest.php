<?php

namespace Modules\ConstructionTrack\Requests;

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
            'signed_date'=>'required|date',
            'health_facility_name'=>'required',
            'facility_type'=>'required',
            'effective_date_from'=>'required|date',
            'type_of_work'=>'required',
            'ohw_contribution'=>'required',
            'engineer_id'=>'required',
            'approval'=>'nullable',
            'district_id'=>'nullable|exists:lkup_districts,id',
            'province_id'=>'nullable|exists:lkup_provinces,id',
            'local_level_id'=>'nullable|exists:lkup_local_levels,id',
            'office_id' => 'nullable',
            'effective_date_to'=>'nullable|date',
            'work_start_date'=>'nullable|date',
            'work_completion_date'=>'nullable|date',
            'donor_codes'=>'nullable|array',
            'donor'=>'nullable',
            'metal_plaque_text'=>'nullable',
        ];
    }
}
