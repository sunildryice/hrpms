<?php

namespace Modules\TravelRequest\Requests\Claim;

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
            'advance_amount' => 'nullable|numeric',
            'reviewer_id' => 'required_if:btn,submit',
            'approver_id' => 'required_if:btn,submit',
            'btn' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'recommendation.recommendation_subject.*.required' => 'Subject is required.',
            'approver_id.required_if' => 'Approver is required.',
        ];
    }
}
