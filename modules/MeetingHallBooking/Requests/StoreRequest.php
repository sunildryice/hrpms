<?php

namespace Modules\MeetingHallBooking\Requests;

use Illuminate\Http\Request;
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
        $valid_date = date('Y-m-d');
        $inputs = Request::all();
        $meeting_date = $inputs['meeting_date'];
        if ($meeting_date == $valid_date ){
            $current_time = date("H:i");
            $start_time = 'required|date_format:H:i|after:'.$current_time;
        }else{
            $start_time = 'required|date_format:H:i|';
        }

        $rules = [
            'meeting_hall_id'=>'required',
            'meeting_date'=>'required|date|after_or_equal:'.$valid_date,
            'start_time'=> $start_time,
            'end_time'=>'required|date_format:H:i|after:start_time',
            'purpose'=>'required',
            'number_of_attendees'=>'required|numeric|max:127|min:2',
            'remarks'=>'required',
            'btn'=>'required',
        ];
        return $rules;
    }

    public function messages()
    {
        return [
            'start_time.after'=>'Start time should not be gone time.',
            'end_time.after'=>'End time should not be before start time.'
        ];
    }
}
