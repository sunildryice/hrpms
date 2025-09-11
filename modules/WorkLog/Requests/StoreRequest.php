<?php

namespace Modules\WorkLog\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


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
            'year' => 'required',
            'month' => ['required',
                'min:1',
                'max:12',
                Rule::unique('work_plans')
                    ->where('year', $this->year)
                    ->where('employee_id', auth()->user()->employee_id)
            ],
        ];
    }

    public function messages()
    {
        return [
            'month.unique' => 'Duplicate entry for monthly log.'
        ];
    }
}
