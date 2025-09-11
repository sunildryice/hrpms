<?php

namespace Modules\TravelRequest\Requests\TravelReport;

use Illuminate\Foundation\Http\FormRequest;

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
            'objectives' =>'required|string',
            'observation'=>'required|string',
            'activities' =>'required|string',
            'other_comments'=>'nullable',
            'recommendation.recommendation_subject.*'=>'nullable',
            'recommendation.recommendation_date.*'=>'nullable',
            'recommendation.recommendation_responsible.*'=>'nullable',
            'recommendation.recommendation_remarks.*'=>'nullable',
            'approver_id'=>'required|exists:users,id',
            'btn'=>'required',
        ];
    }

    public function messages()
    {
        return [
            'recommendation.recommendation_subject.*.required'=>'Subject is required.'
        ];
    }
}

