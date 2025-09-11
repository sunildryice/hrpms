<?php

namespace Modules\TrainingRequest\Requests;

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
            'activity_code_id'=>'required',
            'account_code_id'=>'required',
            'title'=>'required',
            'start_date'=>'required|date',
            'end_date'=>'required|date|after_or_equal:start_date',
            'own_time'=>'required|numeric',
            'work_time'=>'required|numeric',
            'duration'=>'required',
            'course_fee'=>'required|numeric',
            'description'=>'required',
            'attachment'=>'mimes:jpeg,png,pdf|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'end_time.after'=>'End time should not be before start time.'
        ];
    }
}
