<?php

namespace Modules\EmployeeExit\Requests\ExitInterview;

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
            'feedbackAnswers.*'=>'required',
            'ratingAnswers.*'=>'required',
            'btn'=>'required',
            'approver_id'=>'required_if:btn,submit'
        ];
    }

    public function messages()
    {
        return [
            'textarea.*.required'=>"Questions should be answered.",
            'feedbackAnswers.*.required'=>"Feedback questions to be answered.",
            'ratingAnswers.*.required'=>"Rating should be submitted.",
            'approver_id.required_if'=>'Please select an approver.'
        ];
    }
}
