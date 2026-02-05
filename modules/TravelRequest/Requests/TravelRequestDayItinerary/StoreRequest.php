<?php

namespace Modules\TravelRequest\Requests\TravelRequestDayItinerary;

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
            'activity_id' => 'required|exists:project_activities,id',
            'date' => 'required|date',
            'planned_activities' => 'nullable|string|max:2000',
            'accommodation' => 'boolean',
            'air_ticket' => 'boolean',
            'departure_place' => 'nullable|string|max:255|required_if:air_ticket,true',
            'arrival_place' => 'nullable|string|max:255|required_if:air_ticket,true',
            'departure_time' => 'nullable|string|max:50',
            'vehicle' => 'boolean',
            'vehicle_request_form_link' => 'nullable|url|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'date.after_or_equal' => 'The date must be on or after the travel departure date.',
            'date.before_or_equal' => 'The date must be on or before the travel return date.',
            'departure_place.required_if' => 'Departure place is required when air ticket is selected.',
            'arrival_place.required_if' => 'Arrival place is required when air ticket is selected.',
        ];
    }

    /**
     * Prepare the data for validation (optional cleanup).
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'accommodation' => $this->boolean('accommodation', false),
            'air_ticket' => $this->boolean('air_ticket', false),
            'vehicle' => $this->boolean('vehicle', false),
        ]);
    }
}