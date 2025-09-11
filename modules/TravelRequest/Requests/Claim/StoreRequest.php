<?php

namespace Modules\TravelRequest\Requests\Claim;

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
            'objectives' =>'required|string|max:255',
            'observation'=>'required|string|max:255',
            'activities' =>'required|string|max:255',
            'other_comments'=>'nullable',
            'recommendation.recommendation_subject.*'=>'required',
            'recommendation.recommendation_date.*'=>'required|date',
            'recommendation.recommendation_responsible.*'=>'required',
            'recommendation.recommendation_remarks.*'=>'required',
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

