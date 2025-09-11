<?php

namespace Modules\PurchaseRequest\Requests\PurchaseOrder\Combine;

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
            'order_quantity' => 'array',
            'unit_price' => 'array',
            'purchase_request_item_ids' => 'required|array',
            'vat_applicable' => 'array',
        ];
    }

    public function messages()
    {
        return [
            'purchase_request_item_ids.required' => 'At least one item must be checked.',
        ];
    }

}
