<?php

namespace Modules\ConstructionTrack\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatewitProgressRequest extends FormRequest
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
            // 'signed_date'=>'required|date',
            // 'health_facility_name'=>'required',
            // 'facility_type'=>'required',
            // 'effective_date_from'=>'required|date',
            // 'type_of_work'=>'required',
            // 'district_id'=>'nullable|exists:lkup_districts,id',
            // 'province_id'=>'nullable|exists:lkup_provinces,id',
            // 'effective_date_from'=>'nullable|date',
            // 'approver_id'=>'required_if:btn,submit',
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
