<?php

namespace Modules\GoodRequest\Requests\Review;

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
            'approver_id'=>'required_if:status_id,4',
            'log_remarks'=>'required',
        ];
    }

    public function messages()
    {
        return [
            'approver_id.required_if'=>'Recommended to is required when recommend option is selected.',
            'log_remarks.required'=>'Remarks is required.',
        ];
    }
}
