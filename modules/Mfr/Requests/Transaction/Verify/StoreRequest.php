<?php

namespace Modules\Mfr\Requests\Transaction\Verify;

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
            'status_id' => 'required',
            'log_remarks' => 'required|string',
            'recommender_id' => 'exclude_unless:status_id,14|required_if:status_id,14'
        ];
    }

    public function messages()
    {
        return [
            'status_id.required' => 'Status is required',
            'log_remarks.required' => 'Remarks is required',
            'recommender_id.required_if' => 'Recommender is required'
            ];
    }
}
