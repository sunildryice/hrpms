<?php

namespace Modules\Employee\Requests\DocumentUpload;

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
        return route('employees.edit', [$this->employee, 'tab' => 'document-upload-details']);
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
            'signature' => 'required_without:profile_picture|mimes:jpeg,jpg,png|max:2048',
            'profile_picture' => 'required_without:signature|mimes:jpeg,jpg,png|max:2048',
            'cv_attachment' => 'nullable|mimes:pdf|max:2048',
        ];
    }
    public function messages()
    {
        return [
            'signature.mimes' => 'Signature must be a JPEG, JPG, or PNG file.',
            'profile_picture.mimes' => 'Profile picture must be a JPEG, JPG, or PNG file.',
            'cv_attachment.mimes' => 'CV must be a PDF file.',
            'cv_attachment.max' => 'CV must not be larger than 2MB.',
        ];
    }
}
