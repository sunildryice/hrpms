<?php

namespace Modules\Employee\Requests\PaymentDetail;

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
            'payment_item_id' => ['required',
                Rule::unique('employee_payment_master_details')->where(function ($query) {
                    $query->where('payment_master_id', '=', $this->payment);
                }),
            ],
            'amount' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'payment_item_id.required'=>'Payment item is required.',
            'payment_item_id.unique'=>'Payment item is already taken.',
        ];
    }
}
