<?php

namespace Modules\Employee\Requests\Education;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Get the URL to redirect to on a validation error.
     *
     * @return string
     */
    protected function getRedirectUrl()
    {
        return route('employees.edit', [$this->employee, 'tab'=>'education-details']);
    }

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
            'education_level_id'=>'required',
            'institution'=>'required|string',
            'degree'=>'required|string',
            'passed_year'=>'required|numeric',
            'attachment'=>'mimes:jpeg,png,pdf|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'attachment.mimes'=>'Only png,jpg or pdf files are allowed.',
            'attachment.max'=>'Maximum allowed file size is 2MB.',
        ];
    }
}
