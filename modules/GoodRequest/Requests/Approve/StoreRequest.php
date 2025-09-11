<?php

namespace Modules\GoodRequest\Requests\Approve;

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
            'logistic_officer_id'=>'required_if:status_id,'.config('constant.APPROVED_STATUS'),
        ];
    }

    public function messages()
    {
        return [
            'logistic_officer_id.required_if'=>'Logistic officer is required when approve option is selected.',
            'log_remarks.required'=>'Remarks is required.',
            'status.required'=>'Status is required.'
        ];
    }
}
