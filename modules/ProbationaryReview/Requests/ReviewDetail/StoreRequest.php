<?php

namespace Modules\ProbationaryReview\Requests\ReviewDetail;

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
        $valid_date = date('Y-m-d');
        return [
            'performance_improvements'=>'nullable',
            'concern_address_summary'=>'nullable',
            'employee_performance_progress'=>'nullable',
            'objectives_met'=>'nullable',
            'objectives_review_remarks'=>'nullable',
            'objectives_review_date'=>'nullable',
            'development_review_remarks'=>'nullable',
            'development_addressed'=>'nullable',
            'development_review_date'=>'nullable',
            'supervisor_recommendation'=>'required',
            // 'director_recommendation'=>'required',
            'appointment_confirmed'=>'nullable',
            'reason_to_address_difficulty'=>'nullable',
            'probation_extended'=>'nullable',
            'reason_and_improvement_to_extend'=>'required_if:probation_extended,on',
            'next_probation_complete_date'=>'nullable|required_if:probation_extended,on|date|after_or_equal:'.$valid_date,
            'extension_length'=>'nullable|required_if:probation_extended,on|numeric|min:1|max:3',
            'indicator.*'=>'required',
            'btn'=>'required',

        ];
    }

    public function messages()
    {
        return [
//            'end_time.after'=>'End time should not be before start time.'
        ];
    }
}
