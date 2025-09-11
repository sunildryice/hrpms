<?php

namespace Modules\LeaveRequest\Requests\LeaveEncash;

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
            'employee_id' => 'required',
            'leave_type_id' => 'required',
            'available_balance' => 'required',
            'encash_balance' => 'required|lte:available_balance',
            'reviewer_id' => 'required|exists:users,id',
            'approver_id' => 'required|exists:users,id',
            'remarks' => 'nullable',
            'btn' => 'required',
        ];
    }
}
