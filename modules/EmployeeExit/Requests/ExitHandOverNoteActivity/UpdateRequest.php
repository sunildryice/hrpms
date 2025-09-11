<?php

namespace Modules\EmployeeExit\Requests\ExitHandOverNoteActivity;

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
            'activity'=>'nullable|string',
            'activity_code_id'=>'nullable|exists:lkup_activity_codes,id',
            'organization'=>'nullable',
            'phone'=>'nullable',
            'email'=>'nullable|email',
            'phone'=>'nullable|max:17',
            'comments'=>'nullable',
        ];
    }

    public function messages()
    {
        return [
            'log_remarks.required'=>'Remarks is required.'
        ];
    }
}
