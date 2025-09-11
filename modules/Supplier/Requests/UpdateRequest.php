<?php

namespace Modules\Supplier\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'supplier_name'=>[
                'required',
                Rule::unique('suppliers')->ignore($this->supplier),
            ],
            'email_address'=>'nullable|email',
            'contact_person_email_address'=>'nullable|email',
            'supplier_type'=>'required',
            'address1'=>'nullable',
            'address2'=>'nullable',
            'contact_number'=>'nullable',
            'contact_person_name'=>'nullable',
            'vat_pan_number'=>'nullable',
            'account_number'=>[
                'nullable',
                Rule::unique('suppliers')->ignore($this->supplier),
            ],
            'account_name'=>'nullable',
            'bank_name'=>'nullable',
            'branch_name'=>'nullable',
            'swift_code'=>[
                'nullable',
                Rule::unique('suppliers')->ignore($this->supplier),
            ],
            'remarks'=>'nullable',
        ];
    }
}
