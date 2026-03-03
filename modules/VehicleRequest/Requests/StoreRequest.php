<?php

namespace Modules\VehicleRequest\Requests;

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
            'project_code_id'=>'required|exists:projects,id',
            'office_id'=>'required_if:vehicle_request_type_id,1',
            'vehicle_request_type_id'=>'required',
            'office_start_datetime'=>'required_if:vehicle_request_type_id,1|date_format:Y-m-d H:i',
            'office_end_datetime'=>'required_if:vehicle_request_type_id,1|date_format:Y-m-d H:i|after:office_start_datetime',
            'start_datetime'=>'required_if:vehicle_request_type_id,2|date',
            'end_datetime'=>'required_if:vehicle_request_type_id,2|date',
            // 'end_datetime'=>'required_if:vehicle_request_type_id,2|date',
            'purpose_of_travel'=>'nullable',
            'employee_ids'=>'nullable|array',
            'vehicle_type_id'=>'required_if:vehicle_request_type_id,1',
            'vehicle_type_ids'=>'required_if:vehicle_request_type_id,2|array',
            'district_ids'=>'nullable|array',
            'other_remarks'=>'nullable',
            'for_hours_flag'=>'nullable',
            'for_hours'=>'nullable|numeric',
            'for_hours_other_remarks'=>'nullable',
            'pickup_time'=>'nullable',
            'pickup_place'=>'nullable',
            'travel_from'=>'nullable',
            'destination'=>'nullable',
            'extra_travel'=>'nullable|numeric',
            'tentative_cost'=>'nullable|numeric',
            'activity_code_id'=>'nullable|exists:lkup_activity_codes,id',
            'account_code_id'=>'nullable|exists:lkup_account_codes,id',
            'donor_code_id'=>'nullable|exists:lkup_donor_codes,id',
            'procurement_officer' => 'nullable|array',
            'remarks'=>'nullable',
            'approver_id'=>'required_if:vehicle_request_type_id,1',
            'btn'=>'required',
            'tab'=>'nullable',
        ];
    }

    public function messages()
    {
        return [
            'office_end_datetime.after'=>'To date and time should be greater than from date and time.',
            'office_start_datetime.required_if'=>'From date and time required.',
            'office_end_datetime.required_if'=>'To date and time required.',
            'office_id.required_if'=>'Office is required.',
            'vehicle_type_id.required_if'=>'Vehicle type is required.',
            'vehicle_type_ids.required_if'=>'Vehicle types is required.',
            'approver_id.required_if'=>'Approver is required.',
        ];
    }
}
