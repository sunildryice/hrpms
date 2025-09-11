<?php

namespace Modules\GoodRequest\Requests\Handover\Approve;

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
            'handover_status_id'=>'required',
            'log_remarks'=>'required',
        ];
    }

    public function messages()
    {
        return [
            'handover_status_id.required_if'=>'Status is required.',
            'log_remarks.required'=>'Remarks is required.'
        ];
    }
}
