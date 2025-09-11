<?php

namespace Modules\PurchaseRequest\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;
use Modules\PurchaseRequest\Models\PurchaseRequest;

class ForwardRequest extends FormRequest
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
        // $purchaseRequest = PurchaseRequest::find($this->purchase);
        // $reviewerRequired = $purchaseRequest->verificationRequired();

        return [
            // 'reviewer_id'=>[Rule::requiredIf($reviewerRequired)],
            // 'budget_verifier_id'=>[Rule::requiredIf($reviewerRequired)],
            'budget_verifier_id'=>'required',
            'approver_id'=>'required',
        ];
    }

    public function messages()
    {
        return [
            // 'reviewer_id.required_if'=>"The finance reviewer is required.",
            'budget_verifier_id.required_if'=>"The budget verifier is required.",
            'approver_id.required'=>"The approver is required.",
        ];
    }
}
