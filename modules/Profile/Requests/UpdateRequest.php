<?php

namespace Modules\Profile\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Get the URL to redirect to on a validation error.
     *
     * @return string
     */
    protected function getRedirectUrl()
    {
        return route('profile.edit');
    }

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
            'full_name'=>'required|string',
            'personal_email_address'=>'required|email|different:official_email_address',
            'telephone_number'=>'nullable|max:17',
            'mobile_number'=>'required|max:17',
            'marital_status'=>'nullable',
            'gender'=>'required',
            'citizenship_number'=>'nullable|required_with:citizenship_attachment',
            'pan_number'=>'nullable|digits:9|required_with:pan_attachment',
            'citizenship_attachment'=>'nullable|mimes:jpg,png,pdf',
            'pan_attachment'=>'nullable|mimes:jpg,png,pdf',
            'date_of_birth'=>'required|date',
            'religion_id'=>'nullable',
            'caste_id'=>'nullable',
        ];
    }

    public function messages()
    {
        return [];
    }
}
