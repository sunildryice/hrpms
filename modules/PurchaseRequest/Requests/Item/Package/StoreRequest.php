<?php

namespace Modules\PurchaseRequest\Requests\Item\Package;

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
            'package_id' => 'required',
            'quantity'=>'required|numeric|min:0.01',
            'activity_code_id' => 'required|exists:lkup_activity_codes,id',
            'account_code_id' => 'required|exists:lkup_account_codes,id',
            'office_id' => 'required|exists:lkup_offices,id',
            'donor_code_id' => 'nullable|exists:lkup_donor_codes,id',
        ];
    }

    public function messages()
    {
        return [
            'package_id.required' => 'The Package is required.',

        ];
    }
}
