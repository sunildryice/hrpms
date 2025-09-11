<?php

namespace Modules\Employee\Requests;

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
            'employee_code'=>'required|unique:employees|min:1|max:10000',
            'employee_type_id'=>'nullable',
            'full_name'=>'required|string',
            'official_email_address'=>'required|email|unique:employees',
            'personal_email_address'=>'nullable|email|different:official_email_address',
            'telephone_number'=>'nullable|max:17',
            'mobile_number'=>'required|max:17',
            'marital_status'=>'nullable',
            'gender'=>'nullable',
            'citizenship_number'=>'nullable|required_with:citizenship_attachment',
            'pan_number'=>'nullable|digits:9|required_with:citizenship_attachment',
            'citizenship_attachment'=>'nullable|mimes:jpg,png,pdf|max:8',
            'pan_attachment'=>'nullable|mimes:jpg,png,pdf|max:8',
            'signature'=>'nullable|mimes:jpg,png|max:2048',
            'profile_picture'=>'nullable|mimes:jpg,png|max:2048',
            'date_of_birth'=>'nullable|date',
            'religion_id'=>'nullable',
            'caste_id'=>'nullable',
        ];
    }

    public function messages()
    {
        return [
            'title.required'=>'Department name is required.',
            'citizenship_attachment.mimes'=>'Only png,jpg or pdf files are allowed.',
            'citizenship_attachment.max'=>'Maximum allowed file size is 2MB.',
            'pan_attachment.mimes'=>'Only png,jpg or pdf files are allowed.',
            'pan_attachment.max'=>'Maximum allowed file size is 2MB.',
            'signature.mimes'=>'Only png,jpg or pdf files are allowed.',
            'signature.max'=>'Maximum allowed file size is 2MB.',
            'profile_picture.mimes'=>'Only png,jpg or pdf files are allowed.',
            'profile_picture.max'=>'Maximum allowed file size is 2MB.',
        ];
    }
}
