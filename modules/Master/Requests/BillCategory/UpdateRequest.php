<?php

namespace Modules\Master\Requests\BillCategory;

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
                Rule::unique('lkup_bill_categories')->ignore($this->billCategory),
            ]
        ];
    }

    public function messages()
    {
        return [
            'title.required'=>'Bill category is required.',
            'title.unique'=>'Bill category is already taken.',
        ];
    }
}
