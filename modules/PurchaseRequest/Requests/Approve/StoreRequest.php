<?php

namespace Modules\PurchaseRequest\Requests\Approve;

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
            'status_id'=>'required',
            'verifier_id'=>'nullable',
            'approver_id'=>'exclude_unless:status_id,4',
            'log_remarks'=>'required',
        ];
    }

    public function messages()
    {
        return [
            'verifier_id.required_if'=>'Verifier is required.',
            'log_remarks.required'=>'Remarks is required.'
        ];
    }
}
