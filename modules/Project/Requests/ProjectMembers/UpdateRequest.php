<?php

namespace Modules\Project\Requests\ProjectMembers;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'members' => 'required|array',
            'members.*' => 'exists:users,id',
            'focal_person_id' => 'nullable|exists:users,id',
            'team_lead_id' => 'nullable|exists:users,id',
        ];
    }

    protected function failedValidation(Validator $validator)
    {

        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    public function messages()
    {
        return [
            'members.required' => 'Please select at least one member.',
            'members.*.exists' => 'One or more selected members do not exist.',
            'focal_person_id.required' => 'Please select a focal person.',
            'focal_person_id.exists' => 'The selected focal person does not exist.',
            'team_lead_id.exists' => 'The selected team lead does not exist.',
        ];
    }
}
