<?php

namespace Modules\Master\Requests\Province;

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
            'province_name'=>[
                'required',
            ]
        ];
    }

    public function messages()
    {
        return [
            'title.required'=>'Unit name is required.',
            'title.unique'=>'Unit name is already taken.',
        ];
    }
}
