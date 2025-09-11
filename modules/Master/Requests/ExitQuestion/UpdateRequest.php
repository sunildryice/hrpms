<?php

namespace Modules\Master\Requests\ExitQuestion;

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
            'question'=>[
                'required',
                Rule::unique('lkup_exit_questions')->ignore($this->exitQuestion),
            ],
            'answer_type'=>'required',
            'options'=>'nullable|array',
        ];
    }

    public function messages()
    {
        return [
            'question.required'=>'Training question is required.',
        ];
    }
}
