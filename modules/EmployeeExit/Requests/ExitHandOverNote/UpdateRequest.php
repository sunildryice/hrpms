<?php

namespace Modules\EmployeeExit\Requests\ExitHandOverNote;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'duty_description'=>'required',
            'reporting_procedures'=>'required',
            'meeting_description'=>'required',
            'contact_after_exit'=>['required','regex:/^(?:[9][7-8]\d{8}|[\w\.-]+@[a-zA-Z\d\.-]+\.[a-zA-Z]{2,})$/'],
            'approver_id'=>'required_if:btn,submit',
            'btn'=>'required',
        ];
    }

    public function messages()
    {
        return [
            'approver_id.required_if'=>"The approver is required when submitted.",
            'contact_after_exit.regex' => "Please enter a valid phone number or email address."
        ];
    }
}
