<?php

namespace Modules\TravelRequest\Requests\Claim\DsaClaim;

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
            'departure_date' => 'required|date_format:Y-m-d',
            'breakfast' => 'nullable|numeric',
            'lunch' => 'nullable|numeric',
            'dinner' => 'nullable|numeric',
            'incident_cost' => 'nullable|numeric',
            'total_dsa' => 'nullable|numeric',
            'daily_allowance' => 'nullable|numeric',
            'lodging_expense' => 'nullable|numeric',
            'other_expense' => 'nullable|numeric',
            'total_amount' => 'nullable|numeric',
            'remarks' => 'nullable',
            'attachment' => 'nullable|mimes:png,jpg,pdf|max:5120',
        ];
    }

    public function messages()
    {
        return [
            'attachment.mimes' => 'Only png,jpg or pdf files are allowed.',
            'attachment.size' => 'Maximum allowed file size is 5MB.',
            'departure_date.required' => 'Date is required.',
        ];
    }
}
