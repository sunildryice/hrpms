<?php

namespace Modules\WorkLog\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitRequest extends FormRequest
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
            'summary'=>'required_if:btn,submit',
            'planned'=>'required',
            'completed'=>'required_if:btn,submit',
            'approver_id'=>'required_if:btn,submit',
            'btn'=>'required'
        ];
    }

    public function messages()
    {
        return [
            'summary.required_if'=>"The summary is required when submitted",
            'completed.required_if'=>"The completed is required when submitted",
        ];
    }
}
