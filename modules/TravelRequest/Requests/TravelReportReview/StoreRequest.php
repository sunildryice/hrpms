<?php

namespace Modules\TravelRequest\Requests\TravelReportReview;

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
            'status_id'=>'required',
            'log_remarks'=>'nullable',
        ];
    }

    public function messages()
    {
        return [
            // 'log_remarks.required'=>'Remarks is required.'
        ];
    }
}
