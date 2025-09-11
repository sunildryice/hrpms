<?php

namespace Modules\PurchaseOrder\Requests\Item;

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
            'activity_code_id'=>'nullable|exists:lkup_activity_codes,id',
            'account_code_id'=>'nullable|exists:lkup_account_codes,id',
            'donor_code_id'=>'nullable|exists:lkup_donor_codes,id',
            'item_id'=>'nullable|exists:lkup_items,id',
            'unit_id'=>'nullable|exists:lkup_measurement_units,id',
            'specification'=>'required',
            'delivery_date'=>'nullable|date',
            'quantity'=>'required|numeric|min:0.01',
            'unit_price'=>'required|numeric|min:0.01',
            'vat_applicable'=>'nullable',
        ];
    }
}
