<?php

namespace Modules\TrainingRequest\Requests\TrainingRecommend;

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
            'approver_id'=>'exclude_if:status_id,6|required_if:status_id,5',
            'approved_amount' => 'exclude_if:status_id,5|required_if:status_id,6|nullable|numeric',
        ];
    }

    public function messages()
    {
        return [
            'approver_id.required_if'=>"The reviewer is required when Recommended",
            'approved_amount.required_if'=>"The approved amount is required when approved",
            'boolean.*.required_if'=>"Need to be checked when submitted",
        ];
    }
}
