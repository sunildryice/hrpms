<?php

namespace Modules\Master\Requests\ExpenseType;

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
                Rule::unique('lkup_expense_types')->ignore($this->expenseType),
            ]
        ];
    }

    public function messages()
    {
        return [
            'title.required'=>'Expense type is required.',
            'title.unique'=>'Expense type is already taken.',
        ];
    }
}
