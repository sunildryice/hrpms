<?php

namespace Modules\VehicleRequest\Requests\Approve;

use Illuminate\Foundation\Http\FormRequest;

class AssignRequest extends FormRequest
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
            'status_id'=>'required',
            'assigned_vehicle_id'=>'required_if:status_id,6',
            'log_remarks'=>'required',
        ];
    }

    public function messages()
    {
        return [
            'assigned_vehicle_id.required_if'=>'Vehicle is required.',
            'log_remarks.required'=>'Remarks is required.',
        ];
    }
}
