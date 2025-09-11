<?php

namespace Modules\PurchaseRequest\Requests\Grn;

use Illuminate\Foundation\Http\FormRequest;

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
            'discount_amount'=>'nullable|numeric|min:0',
            'purchase_request_item_ids'=>'required|array',
            'unit_price'=>'array',
            'received_quantity'=>'array',
            'vat_applicable'=>'array'
        ];
    }

    public function messages()
    {
        return [
            'purchase_request_item_ids.required'=>'At least one item must be checked.'
        ];
    }
}
