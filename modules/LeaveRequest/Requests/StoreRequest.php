<?php

namespace Modules\LeaveRequest\Requests;

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
            'leave_type_id' => 'required',
            'start_date' => 'required|date|before_or_equal:end_date',
            'end_date' => 'required|date|after_or_equal:end_date',
            'substitute_id' => 'nullable|exists:employees,id',
            'approver_id' => 'required|exists:users,id',
            'remarks' => 'required|string',
            'leave_days' => 'required|array',
            'leave_mode_id' => 'required|array',
            'leave_time' => 'nullable|array',
            'attachment' => 'mimes:jpg,png,pdf|max:2048',
            'substitutes' => 'nullable|array',
            'btn' => 'required',
        ];
    }
}
