<?php

namespace Modules\Master\Requests\ExitRating;

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
                Rule::unique('lkup_exit_ratings')->ignore($this->exitRating),
            ]
        ];
    }

    public function messages()
    {
        return [
            'title.required'=>'Expense category is required.',
            'title.unique'=>'Expense category is already taken.',
        ];
    }
}
