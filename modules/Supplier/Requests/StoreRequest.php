<?php

namespace Modules\Supplier\Requests;

use Illuminate\Validation\Rule;
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
            'supplier_type'=>'required',
            'supplier_name'=>'required|unique:suppliers',
            'email_address'=>'nullable|email',
            'contact_person_email_address'=>'nullable|email',
            'address1'=>'nullable',
            'address2'=>'nullable',
            'contact_number'=>'nullable',
            'contact_person_name'=>'nullable',
            'vat_pan_number'=>'nullable',
            'account_number'=>[
                'nullable',
                Rule::unique('suppliers'),
            ],
            'account_name'=>'nullable',
            'bank_name'=>'nullable',
            'branch_name'=>'nullable',
            'swift_code'=>[
                'nullable',
                Rule::unique('suppliers'),
            ],
            'remarks'=>'nullable',
        ];
    }

    public function messages()
    {
        return [
            'title.required'=>'Department name is required.'
        ];
    }
}
