<?php

namespace Modules\AdvanceRequest\Requests\Detail;

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
            'account_code_id'=>'required|exists:lkup_account_codes,id',
            'donor_code_id'=>'nullable|exists:lkup_donor_codes,id',
            'description'=>'required',
            'amount'=>'required|numeric|min:0.01',
            'attachment'=>'nullable|mimes:jpeg,png,pdf|max:2048',
        ];
    }
}
