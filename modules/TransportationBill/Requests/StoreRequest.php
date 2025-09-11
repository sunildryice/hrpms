<?php

namespace Modules\TransportationBill\Requests;

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
            // 'bill_number'=>'required',
            'bill_date'=>'required|date',
            'shipper_name'=>'required',
            'shipper_address'=>'required',
            'consignee_name'=>'required',
            'consignee_address'=>'required',
            'remarks'=>'nullable',
            'instruction'=>'nullable',
        ];
    }
}
