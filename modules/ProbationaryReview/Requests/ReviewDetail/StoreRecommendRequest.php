<?php

namespace Modules\ProbationaryReview\Requests\ReviewDetail;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRecommendRequest extends FormRequest
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
        return [
            'status_id'=>'required',
            'btn'=>'required',

        ];
    }

    public function messages()
    {
        return [
//            'end_time.after'=>'End time should not be before start time.'
        ];
    }
}
