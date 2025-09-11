<?php

namespace Modules\PurchaseRequest\Requests\Item;

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
            'activity_code_id'=>'required|exists:lkup_activity_codes,id',
            'account_code_id'=>'required|exists:lkup_account_codes,id',
            'donor_code_id'=>'required|exists:lkup_donor_codes,id',
            'item_id'=>'required|exists:lkup_items,id',
            'unit_id'=>'required|exists:lkup_measurement_units,id',
            // 'district_id' => 'required|exists:lkup_districts,id',
            'office_id' => 'required|exists:lkup_offices,id',
            'specification'=>'required',
            'quantity'=>'required|numeric|min:0.01',
            'unit_price'=>'required|numeric|min:0.01',
        ];
    }
}
