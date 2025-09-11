<?php

namespace Modules\Master\Requests\ProjectCode;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'title' => [
                'required',
                Rule::unique('lkup_project_codes')->ignore($this->projectCode),
            ],
            'short_name' => [
                'sometimes',
                'nullable',
                Rule::unique('lkup_project_codes')->ignore($this->projectCode),
            ],
            'description' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Project code is required.',
            'title.unique' => 'Project code is already taken.',
        ];
    }
}
