<?php

namespace Modules\FundRequest\Requests\Activity;

use Illuminate\Foundation\Http\FormRequest;

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
            'activity_code_id'=>'required|exists:lkup_activity_codes,id',
            'estimated_amount'=>'required|numeric|min:1',
            'budget_amount'=>'required|numeric|min:0',
            'project_target_unit'=>'nullable',
            'dip_target_unit'=>'nullable',
            'justification_note'=>'nullable',
        ];
    }
}
