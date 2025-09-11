<?php

namespace Modules\Contract\Requests;

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
            'supplier_id'=>'required|exists:suppliers,id',
            'contract_number'=>'required',
            'description'=>'nullable',
            'contact_name'=>'nullable',
            'contact_number'=>'nullable',
            'address'=>'nullable',
            'contract_date'=>'required|date',
            'effective_date'=>'required|date',
            'expiry_date'=>'required|date|after_or_equal:effective_date',
            'reminder_days'=>'nullable|digits_between:1,90',
            'termination_days'=>'nullable|digits_between:1,90',
            'contract_amount'=>'required|numeric',
            'focal_person_id'=>'nullable|exists:employees,id',
            'attachment'=>'nullable|mimes:png,jpg,pdf|max:2048',
            'remarks'=>'nullable',
        ];
    }

    public function messages()
    {
        return [
            'supplier_id.required'=>'Organization/Individual is required.',
            'supplier_id.exists'=>'Organization/Individual is invalid.',
            'attachment.mimes'=>'Only png,jpg or pdf files are allowed.',
            'attachment.size'=>'Maximum allowed file size is 2MB.',
        ];
    }
}
