<?php

namespace Modules\PaymentSheet\Requests;

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
            'supplier_id'           => 'required|exists:suppliers,id',
            'district_id'           => 'nullable',
            'purchase_order_ids'    => 'nullable',
            'purpose'               => 'nullable'
//            'purchase_order_ids'=>'required|array',
        ];
    }
}
