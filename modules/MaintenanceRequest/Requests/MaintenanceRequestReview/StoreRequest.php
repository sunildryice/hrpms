<?php

namespace Modules\MaintenanceRequest\Requests\MaintenanceRequestReview;

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
            'status_id'=>'required',
            'log_remarks'=>'required',
            'logistic_officer_id'=>'required_if:status_id,6',
        ];
    }

    public function messages()
    {
        return [
            'logistic_officer_id.required_if'=>'Logistic officier is required when approved.',
            'log_remarks.required'=>'Remarks is required.'
        ];
    }
}
