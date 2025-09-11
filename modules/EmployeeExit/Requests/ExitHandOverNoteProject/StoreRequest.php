<?php

namespace Modules\EmployeeExit\Requests\ExitHandOverNoteProject;

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
            'project'=>'nullable|string',
            'project_code_id'=>'nullable|exists:lkup_project_codes,id',
            'project_status'=>'nullable',
            'action_needed'=>'nullable',
            'partners'=>'nullable',
            'budget'=>'nullable|numeric|min:0.01',
            'critical_issues'=>'nullable',
        ];
    }


}
