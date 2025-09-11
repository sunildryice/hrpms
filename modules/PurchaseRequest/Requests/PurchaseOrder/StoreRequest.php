<?php

namespace Modules\PurchaseRequest\Requests\PurchaseOrder;

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
            'district_ids'=>'required|array',
            'supplier_id'=>'required|exists:suppliers,id',
            'lta_contract_id' => 'nullable|exists:lta_contracts,id',
            'delivery_date' => 'required',
            'delivery_location' => 'nullable',
            'delivery_instructions' => 'nullable',
            'currency_id' => 'nullable',
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

    // public function validated($key = null, $default = null)
    // {
    //     $validatedInputs = $this->validator->validated();
    //     $poUnitPrices = $validatedInputs['unit_price'];
    //     foreach ($poUnitPrices as $unitPrice) {
    //         if ($unitPrice == 0 || empty($unitPrice)) {
    //             $this->validator->errors()->add('unit_price', 'Unit price cannot be empty/zero.');
    //         }
    //     }
    //     return data_get($validatedInputs, $key, $default);
    // }

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
