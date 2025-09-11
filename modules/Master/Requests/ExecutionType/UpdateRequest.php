<?php

namespace Modules\Master\Requests\ExecutionType;

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
                Rule::unique('lkup_executions')->ignore($this->execution),
            ],
            'description'=>'nullable'
        ];
    }

    public function messages()
    {
        return [
            'title.required'=>'Execution title is required.',
            'title.unique'=>'Execution Title is already taken.',
        ];
    }
}
