<?php

namespace Modules\ProbationaryReview\Requests;

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
            'employee_id'=>'required',
            'date'=>'required|date',
            'review_id'=>'required',
            'reviewer_id'=>'required',
            'approver_id'=>'nullable',
            'remarks'=>'required',
            'btn'=>'nullable',
        ];
    }

    public function messages()
    {
        return [
            'end_time.after'=>'End time should not be before start time.'
        ];
    }
}
