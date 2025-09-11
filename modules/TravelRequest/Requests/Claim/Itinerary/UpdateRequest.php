<?php

namespace Modules\TravelRequest\Requests\Claim\Itinerary;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'percentage_charged'=>'required|numeric|min:0',
            'office_id' => 'required|exists:lkup_offices,id',
            'description' => 'nullable',
            'attachment'=>'nullable|mimes:png,jpg,pdf|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'attachment.mimes'=>'Only png,jpg or pdf files are allowed.',
            'attachment.size'=>'Maximum allowed file size is 2MB.',
        ];
    }
}
