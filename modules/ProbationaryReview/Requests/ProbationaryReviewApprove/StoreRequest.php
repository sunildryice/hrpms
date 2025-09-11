<?php

namespace Modules\ProbationaryReview\Requests\ProbationaryReviewApprove;

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
            'director_recommendation'=>'required',
            'status_id'=>'required',
        ];
    }

    public function messages()
    {
        return [
            'end_time.after'=>'End time should not be before start time.'
        ];
    }
}
