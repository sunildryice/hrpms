<?php

namespace Modules\TravelRequest\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\TravelRequest\Models\TravelRequest;

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
        $travelRequest = TravelRequest::find($this->travelRequest);
        $valid_date = $travelRequest->parentTravelRequest ? $travelRequest->parentTravelRequest->departure_date->subDays(14)->format('Y-m-d') : date('Y-m-d');

        return [
            'travel_type_id'=>'required',
            'purpose_of_travel'=>'required',
            'project_code_id'=>'required|exists:projects,id',
            'departure_date'=>'required|date|after_or_equal:'.$valid_date,
            'return_date'=>'required|date|after_or_equal:departure_date',
            'accompanying_staff'=>'nullable|array',
            'substitutes'=>'nullable|array',
            'final_destination'=>'required',
            'remarks'=>'nullable',
            'approver_id'=>'required|exists:users,id',
            'btn'=>'required',
            'employee_id'=>'nullable',
        ];
    }

    public function messages()
    {
        return [
            'departure_date.after_or_equal'=>'Departure Date should not be before than today.',
            'return_date.after'=>'Return Date should not be before departure date.',
            'approver_id.required'=>'Approver is required.',
        ];
    }
}
