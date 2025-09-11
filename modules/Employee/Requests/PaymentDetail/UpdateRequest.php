<?php

namespace Modules\Employee\Requests\PaymentDetail;

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
//            'payment_item_id'=>'required',
            'amount'=>'required|numeric',
        ];
    }

    public function messages()
    {
        return [];
    }
}
