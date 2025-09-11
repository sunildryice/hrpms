<?php

namespace Modules\TrainingRequest\Requests\TrainingResponse;

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
            'log_remarks' => 'required_if:status_id,2',
            'textarea.*' => 'required_if:status_id,4',
            'btn' => 'required'
        ];
    }

    public function messages()
    {
        return [
            // 'reviewer_id.required_if'=>"The reviewer is required when submitted",
            'boolean.*.required_if'=>"Need to be checked when submitted",
        ];
    }
}
