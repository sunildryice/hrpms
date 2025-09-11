<?php

namespace Modules\GoodRequest\Requests\Assign\Direct;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'employee_id' => 'required|exists:employees,id',
            'room_number' => 'nullable|string',
            'handover_date' => 'nullable|date',
            'approver_id' => 'required|exists:users,id',
        ];
    }

    public function messages()
    {
        return [];
    }
}
