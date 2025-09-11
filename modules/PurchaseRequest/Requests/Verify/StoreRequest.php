<?php

namespace Modules\PurchaseRequest\Requests\Verify;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\PurchaseRequest\Models\PurchaseRequest;

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
        $purchaseRequest = PurchaseRequest::find($this->purchase);
        $reviewerRequired = $purchaseRequest->verificationRequired();

        return [
            'status_id'=>'required_if:btn,submit',
            'reviewer_id'=>[Rule::requiredIf($this->status_id == 14 && $reviewerRequired)],
            'log_remarks'=>'required_if:btn,submit',
            'btn'=>'required',
        ];
    }

    public function messages()
    {
        return [
            'status_id.required_if'=>'This field is required when submitted.',
            'reviewer_id.required_if'=>'Reviewer is required.',
            'log_remarks.required_if'=>'Remarks is required when submitted.',
        ];
    }
}
