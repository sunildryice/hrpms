<?php

namespace Modules\Lta\Requests;

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
            'supplier_id' => 'required|exists:suppliers,id',
            'office_id' => 'required|exists:lkup_offices,id',
            'contract_number' => 'required',
            'description' => 'nullable',
            'contract_date' => 'required|date',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            // 'contract_amount' => 'required|numeric',
            'focal_person_id' => 'nullable|exists:employees,id',
            'attachment' => 'nullable|mimes:png,jpg,pdf|max:2048',
            'remarks' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'supplier_id.required' => 'Organization/Individual is required.',
            'supplier_id.exists' => 'Organization/Individual is invalid.',
            'attachment.mimes' => 'Only png,jpg or pdf files are allowed.',
            'attachment.size' => 'Maximum allowed file size is 2MB.',
        ];
    }
}
