<?php

namespace Modules\TravelRequest\Requests\TravelReport;

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
            'objectives' => 'required|string',
            'major_achievement' => 'required|string',
            'not_completed_activities' => 'required|string',
            'conclusion_recommendations' => 'nullable',
            'total_travel_days' => 'nullable|integer|min:1',

            'recommendation.day_number.*' => 'nullable|string',
            'recommendation.activity_date.*' => 'nullable|date',
            'recommendation.completed_tasks.*' => 'nullable|string',
            'recommendation.remarks.*' => 'nullable|string',

            'btn' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'recommendation.activity_date.*.required' => 'Date is required for each day.',
            'recommendation.activity_date.*.date' => 'Please enter a valid date.',
            'recommendation.completed_tasks.*.required' => 'Please describe the activities completed on this day.',
        ];
    }
}

