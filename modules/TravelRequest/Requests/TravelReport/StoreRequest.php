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
            'not_completed_activities' => 'nullable|string',
            'conclusion_recommendations' => 'nullable',
            'total_travel_days' => 'nullable|integer|min:1',

            'itinerary.itinerary_id.*' => 'required|integer|exists:travel_request_day_itineraries,id',
            'itinerary.completed_tasks.*' => 'nullable|string',
            'itinerary.remarks.*' => 'nullable|string',

            'btn' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'itinerary.completed_tasks.*.required' => 'Please describe the activities completed on this day.',
        ];
    }
}

