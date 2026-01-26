<?php

namespace Modules\Project\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            // 'description' => 'required|string',
            'start_date' => 'required|date',
            'completion_date' => 'required|date|after_or_equal:start_date',
            'team_lead_id' => 'required|exists:users,id',
            'focal_person_id' => 'required|exists:users,id',
            'members' => 'required|array',
            'members.*' => 'exists:users,id',
            'focal_person_id' => 'required|exists:users,id',
            'team_lead_id' => 'required|exists:users,id',
            'stages' => 'required|array',
            'stages.*' => 'exists:lkup_activity_stages,id',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        return parent::failedValidation($validator);
    }

    public function messages()
    {
        return [
            'title.required' => 'The project title is required.',
            'completion_date.after_or_equal' => 'The completion date must be a date after or equal to the start date.',
            'members.required' => 'At least one project member must be selected.',
            'stages.*.exists' => 'One or more selected stages are invalid.',
        ];
    }
}
