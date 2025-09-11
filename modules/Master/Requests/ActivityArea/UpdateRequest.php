<?php

namespace Modules\Master\Requests\ActivityArea;

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
            'title'=>[
                'required',
                Rule::unique('lkup_activity_areas')->ignore($this->activityCode),
            ],
            'description'=>'nullable',
        ];
    }

    public function messages()
    {
        return [
            'title.required'=>'Activity area is required.',
            'title.unique'=>'Activity area is already taken.',
        ];
    }
}
