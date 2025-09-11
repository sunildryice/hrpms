<?php

namespace Modules\Master\Requests\LeaveType;

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
            'short_description'=>'nullable',
            'number_of_days'=>'nullable|numeric|max:365',
            'leave_frequency'=>'required',
            'leave_basis'=>'required',
            'maximum_carry_over'=>'nullable|numeric',
            'paid'=>'required',
            'title' => [
                'required',
                Rule::unique('lkup_leave_types')->where(function($query) {
                    $query->where('fiscal_year_id', '=', request()->get('fiscal_year_id'));
                })
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.required'=>'Leave name is required.',
            'title.unique'=>'Leave name is already taken.',
            'fiscal_year_id.required'=>'FY is required.',
            'leave_frequency.required'=>'Month/Year is required.',
        ];
    }
}
