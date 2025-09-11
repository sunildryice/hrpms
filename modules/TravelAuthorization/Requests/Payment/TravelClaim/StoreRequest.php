<?php

namespace Modules\TravelAuthorization\Requests\Payment\TravelClaim;

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
            'pay_date'=>'required|date',
            'payment_remarks'=>'required',
            'btn' => 'nullable'
        ];
    }

    public function messages()
    {
        return [
            'payment_remarks.required'=>'Remarks is required.'
        ];
    }
}
