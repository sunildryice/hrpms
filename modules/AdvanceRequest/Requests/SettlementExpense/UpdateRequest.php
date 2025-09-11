<?php

namespace Modules\AdvanceRequest\Requests\SettlementExpense;

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
            'district_id'=>'required',
            'location'=>'nullable',
            'narration'  => 'nullable',
            'account_code_id'  => 'required',
            'activity_code_id'  => 'required',
            'donor_code_id'  => 'nullable',
        ];
    }
}
