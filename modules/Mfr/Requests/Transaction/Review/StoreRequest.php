<?php

namespace Modules\Mfr\Requests\Transaction\Review;

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
            'verifier_id' => 'exclude_unless:status_id,11|required_if:status_id,11'
        ];
    }

    public function messages()
    {
        return [
            'status_id.required' => 'Status is required',
            'log_remarks.required' => 'Remarks is required',
            'verifier_id.required_if' => 'Verifier is required'
            ];
    }
}
