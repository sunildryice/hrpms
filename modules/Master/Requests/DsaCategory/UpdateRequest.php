<?php

namespace Modules\Master\Requests\DsaCategory;

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
                Rule::unique('lkup_dsa_categories')->ignore($this->dsaCategory),
            ],
            'rate'=>'required|numeric|min:1',
            'description'=>'nullable'
        ];
    }

    public function messages()
    {
        return [
            'title.required'=>'DSA category is required.',
            'title.unique'=>'DSA category is already taken.',
        ];
    }
}
