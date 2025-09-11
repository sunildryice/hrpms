<?php

namespace Modules\Profile\Requests\MedicalCondition;

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
        return route('profile.edit', ['tab'=>'medicalInformation']);
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
            'blood_group_id'=>'required|exists:lkup_blood_groups,id',
            'medical_condition'=>'nullable|string|max:191',
            'remarks'=>'nullable|string',
        ];
    }
}
