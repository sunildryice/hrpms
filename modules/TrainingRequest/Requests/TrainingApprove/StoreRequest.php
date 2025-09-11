<?php

namespace Modules\TrainingRequest\Requests\TrainingApprove;

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
            'approved_amount'=>'min:0|required_if:status_id,6',
        ];
    }

    public function messages()
    {
        return [
            // 'reviewer_id.required_if'=>"The reviewer is required when submitted",
            'approved_amount.required_if'=>"Approved amount required when approve.",
        ];
    }
}
