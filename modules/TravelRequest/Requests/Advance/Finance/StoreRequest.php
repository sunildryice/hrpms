<?php

namespace Modules\TravelRequest\Requests\Advance\Finance;

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
            // 'travel_request_id' => 'required|exists:travel_requests,id',
            'received_advance_amount' => 'required|numeric',
            'advance_received_at' => 'required|date',
            'finance_remarks' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'received_advance_amount.required'=>'Advance amount is required',
            'advance_received_at.required'=>'Advance date is required'
        ];
    }
}
