<?php

namespace Modules\Master\Requests\TrainingQuestion;

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
                Rule::unique('lkup_training_questions')->ignore($this->trainingQuestion),
            ],
            'type'=>'required',
            'answer_type'=>'required'
        ];
    }

    public function messages()
    {
        return [
            'question.required'=>'Training question is required.',
        ];
    }
}
