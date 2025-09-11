<?php

namespace Modules\Master\Requests\ActivityCode;

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
            'title'=>'required|unique:lkup_activity_codes',
            'description'=>'nullable'
        ];
    }

    public function messages()
    {
        return [
            'title.required'=>'Activity code is required.',
            'title.unique'=>'Activity code is already taken.',
        ];
    }
}
