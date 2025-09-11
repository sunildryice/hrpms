<?php

namespace Modules\AdvanceRequest\Requests;

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
            'required_date'=>'required|date',
            'start_date'=>'required|date',
            'settlement_date'=>'required|date',
            'end_date'=>'required|date',
            'district_id'=>'nullable|exists:lkup_districts,id',
            'request_for_office_id'=>'nullable|exists:lkup_offices,id',
            'project_code_id'=>'nullable|exists:lkup_project_codes,id',
            'purpose'=>'required',
            'approver_id'=>'required_if:btn,submit',
            'verifier_id'=>'required_if:btn,submit',
            'btn'=>'required',
        ];
    }

    public function messages()
    {
        return [
            'approver_id.required_if'=>"The approver is required when submitted",
            'verifier_id.required_if'=>"The verifier is required when submitted",
        ];
    }
}
