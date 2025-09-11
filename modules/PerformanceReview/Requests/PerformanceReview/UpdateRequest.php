<?php

namespace Modules\PerformanceReview\Requests\PerformanceReview;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

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
        return [
            'review_from'       => 'required|date',
            'review_to'         => 'required|date|after:review_from',
            'deadline_date'     => 'required'
        ];
    }

    public function messages()
    {
        return [
            'review_from.required'  => '\'Review From\' date is required.',
            'review_to.required'    => '\'Review To\' date is required.',
            'deadline_date.required'    => 'Deadline date is required.'
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
            'review_from'       => 'Review From date',
            'review_to'         => 'Review To date',
            'deadline_date'     => 'Deadline date'
        ];
    }
}
