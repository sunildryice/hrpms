<?php

namespace Modules\ConstructionTrack\Requests\ConstructionInstallmentReview;

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
            'approver_id'   => 'required_if:status_id,'.config('constant.VERIFIED_STATUS'),
            'status_id'     => 'required',
            'remarks'       => 'required'
        ];
    }

    public function attributes()
    {
        return [
            'approver_id'   => 'approver',
            'status_id'     => 'status',
        ];  
    }

    public function messages()
    {
        return [
            'required_if' => 'Please select an approver if the status is verified.'
        ];
    }
}
