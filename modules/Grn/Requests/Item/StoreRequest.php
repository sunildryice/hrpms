<?php

namespace Modules\Grn\Requests\Item;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Grn\Models\GrnItem;

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
            'item_id'=>'required',
            'unit_id'=>'required',
            'quantity'=>'required|numeric|min:1',
            'unit_price'=>'required|numeric|min:0',
            'discount_amount'=>'nullable|numeric|min:0',
            'activity_code_id'=>'nullable',
            'account_code_id'=>'nullable',
            'donor_code_id'=>'nullable',
            'specification'=>'nullable'
        ];
    }
}
