<?php

namespace Modules\EventCompletion\Requests;

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
            'district_id'=>'required',
            'activity_code_id'=>'required|exists:lkup_activity_codes,id',
            'venue'=>'required',
            'start_date'=>'required|date',
            'end_date'=>'required|date|after_or_equal:start_date',
            'background'=>'nullable',
            'objectives'=>'required',
            'process'=>'required',
            'closing'=>'nullable',
        ];
    }

    public function messages()
    {
        return [];
    }
}
