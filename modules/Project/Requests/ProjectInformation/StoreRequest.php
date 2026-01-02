<?php

namespace Modules\Project\Requests\ProjectInformation;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

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
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'completion_date' => 'nullable|date|after_or_equal:start_date',
            'team_lead_id' => 'nullable|exists:users,id',
            'focal_person_id' => 'nullable|exists:users,id',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        dd('validation failed in StoreRequest', $validator->errors());
        return parent::failedValidation($validator);
    }
}
