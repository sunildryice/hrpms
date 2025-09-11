<?php

namespace Modules\Memo\Requests;

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
        $rules = [
            'memo_to'=>'required',
            'memo_through'=>'nullable',
            // 'memo_from'=>'required',
            'memo_date'=>'required|date|before_or_equal:'.$valid_date,
            'subject'=>'required',
            'description'=>'nullable',
            'enclosure'=>'nullable',
            'attachment'=>'nullable|mimes:jpeg,png,pdf|max:2048',
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
