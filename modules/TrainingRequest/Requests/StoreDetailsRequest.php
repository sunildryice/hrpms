<?php

namespace Modules\TrainingRequest\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDetailsRequest extends FormRequest
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
            'textarea.*'=>'required',
            'boolean.*'=>'required_if:btn,submit',
            'reviewer_id'=>'required_if:btn,submit',
            'recommender_id'=>'required_without:approver_id',
            'approver_id'=>'required_without:recommender_id',
            'btn'=>'required',

        ];
    }

    public function messages()
    {
        return [
            'reviewer_id.required_if'=>"The reviewer is required when submitted",
            'recommender_id.required_without'=>"The recommender is required if no approver.",
            'approver_id.required_without'=>"The approver is required if no recommender",
            'boolean.*.required_if'=>"Need to be checked when submitted",
        ];
    }
}
