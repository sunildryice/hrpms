<?php

namespace Modules\DistributionRequest\Requests\Item;

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
            'donor_code_id'=>'nullable|exists:lkup_donor_codes,id',
            'unit_price'=>'required|numeric|min:0.01',
//            'inventory_item_id'=>'required|exists:inventory_items,id',
            'specification'=>'nullable',
            'available_quantity'=>'required|numeric',
            'quantity'=>'required|numeric|min:1|lte:available_quantity',
        ];
    }
}
