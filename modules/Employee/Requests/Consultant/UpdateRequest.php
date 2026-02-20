<?php

namespace Modules\Employee\Requests\Consultant;

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
        return route('consultant.edit', [$this->consultant]);
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
            'ste_code' => [
                'required',
                Rule::unique('employees')->ignore($this->consultant),
                'min:1',
                'max:10000'
            ],
            'employee_type_id' => 'nullable',
            'full_name' => 'required|string',
            'official_email_address' => [
                'nullable',
                'email',
                // Rule::unique('employees')->ignore($this->employee),
            ],
            'personal_email_address' => 'nullable|email|different:official_email_address',
            'telephone_number' => 'nullable|max:17',
            'mobile_number' => 'required|max:17',
            'marital_status' => 'nullable',
            'gender' => 'nullable',
            'citizenship_number' => 'nullable|required_with:citizenship_attachment',
            'pan_number' => 'nullable|digits:9|required_with:pan_attachment',
            'citizenship_attachment' => 'nullable|mimes:jpg,png,pdf|max:2048',
            'pan_attachment' => 'nullable|mimes:jpg,png,pdf|max:2048',
            'signature' => 'nullable|mimes:jpg,png|max:2048',
            'profile_picture' => 'nullable|mimes:jpg,png|max:2048',
            'date_of_birth' => 'nullable|date',
            'probation_complete_date' => 'nullable|date',
            'religion_id' => 'nullable',
            'caste_id' => 'nullable',

            'nid_number' => 'nullable|string|max:50',
            'passport_number' => 'nullable|string|max:20',
            'passport_attachment' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'vehicle_license_number' => 'nullable|string|max:50',
            'vehicle_license_category' => 'nullable|array',
            'vehicle_license_category.*' => 'string|in:A,B,C,D,E,F,G,H,K',

            'earn_leave' => 'nullable',
            'leave_percentage' => 'nullable|integer|in:100,75,50|required_if:earn_leave,true',
        ];
    }

    public function messages()
    {
        return [
            'ste_code.required' => 'Consultant code is required.',
            'title.required' => 'Department name is required.',
            'citizenship_attachment.mimes' => 'Only png,jpg or pdf files are allowed.',
            'citizenship_attachment.max' => 'Maximum allowed file size is 2MB.',
            'pan_attachment.mimes' => 'Only png,jpg or pdf files are allowed.',
            'pan_attachment.max' => 'Maximum allowed file size is 2MB.',
            'signature.mimes' => 'Only png,jpg or pdf files are allowed.',
            'signature.max' => 'Maximum allowed file size is 2MB.',
            'profile_picture.mimes' => 'Only png,jpg or pdf files are allowed.',
            'profile_picture.max' => 'Maximum allowed file size is 2MB.',
        ];
    }
}
