<?php

namespace Modules\PerformanceReview\Requests\PerformanceReviewReview;

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
            'status_id'     => 'required',
            'log_remarks'   => 'nullable',
            'receiver_id'   => 'required_if:status_id,'.config('constant.VERIFIED_STATUS'),
        ];
    }

    public function messages()
    {
        return [
            'status_id.required'    => 'Status is required.',
            // 'log_remarks.required'  => 'Remarks is required.',
            'required_if'           => 'Please select a receiver if the status is verified.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'status_id'     => 'Status',
            'log_remarks'   => 'Remarks',
        ];
    }
}
