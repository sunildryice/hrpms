<?php

namespace Modules\PurchaseOrder\Requests\Item;

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
            'purchase_request_item_ids'=>'required|array',
            'vat_applicable'=>'array',
            'order_quantity'=>'array',
            'unit_price'=>'array',
        ];
    }

    public function messages()
    {
        return [
            'purchase_request_item_ids.required'=>'At least one item must be checked.'
        ];
    }


    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $validatedInputs = $validator->validated();
            $poItemIds = $validatedInputs['purchase_request_item_ids'];
            $poUnitPrices = $validatedInputs['unit_price'];

            foreach ($poUnitPrices as $key => $unitPrice) {
                if (in_array($key, $poItemIds)) {
                    if ($unitPrice == 0 || empty($unitPrice)) {
                        $validator->errors()->add('unit_price', 'PO Amount cannot be empty/zero.');
                    }
                }
            }
        });
    }
}
