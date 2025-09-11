<?php

namespace Modules\TrainingRequest\Requests\TrainingReport;

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
            'textarea.*'=>'required',
            'reviewer_id'=>'required_if:btn,submit',
            'btn'=>'required',

        ];
    }

    public function messages()
    {
        return [
            // 'reviewer_id.required_if'=>"The reviewer is required when submitted",
            'reviewer_id.required_if'=>"Select Reviewer before submit.",
        ];
    }
}
