<?php

namespace Modules\Master\Requests\FamilyRelation;

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
                Rule::unique('lkup_family_relations')->ignore($this->familyRelation),
            ]
        ];
    }

    public function messages()
    {
        return [
            'title.required'=>'Family Relation is required.',
            'title.unique'=>'Family Relation is already taken.',
        ];
    }
}
