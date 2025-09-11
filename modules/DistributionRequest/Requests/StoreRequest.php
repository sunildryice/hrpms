<?php

namespace Modules\DistributionRequest\Requests;

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
            'district_id' => 'nullable|exists:lkup_districts,id',
            'office_id' => 'nullable|exists:lkup_offices,id',
            'project_code_id' => 'nullable|exists:lkup_project_codes,id',
            // 'health_facility_name'=>'required',
            'health_facility_id' => 'required',
            'remarks' => 'nullable',
            'purchase_request_ids' => 'nullable|array',
        ];
    }
}
