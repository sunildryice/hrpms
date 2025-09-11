<?php

namespace Modules\Mfr\Requests\Transaction;

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
            'transaction_type' => 'required|numeric',
            'transaction_date' => 'required|date',
            'release_amount' => 'nullable|numeric',
            'expense_amount' => 'nullable|numeric',
            'reimbursed_amount' => 'nullable|numeric',
            'questioned_cost' => 'required|numeric',
            'remarks' => 'required|string',
            'question_remarks' => 'nullable',
            'reviewer_id' => 'required|exists:users,id',
            'btn' => 'required|string',
       ];
    }
}
