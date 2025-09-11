<?php

namespace Modules\PurchaseOrder\Requests\PaymentSheet;

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
            'payment_bill_id' => 'required',
            'tds_percentage' => 'required|numeric',
            'district_id' => 'nullable',
            'purpose' => 'nullable',
            'purchase_order_ids' => 'required|array',
        ];
    }
}
