<?php

namespace Modules\PurchaseRequest\Requests\PurchaseOrder\Combine;

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
            'purchase_order_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'purchase_order_id.required' => 'Purchase Order is required.',
        ];
    }

}
