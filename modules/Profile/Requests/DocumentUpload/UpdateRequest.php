<?php

namespace Modules\Profile\Requests\DocumentUpload;

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
        return route('profile.edit', ['tab' => 'document-upload-details']);
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
            'signature' => 'nullable|mimes:jpeg,jpg,png|max:2048',
            'profile_picture' => 'nullable|mimes:jpeg,jpg,png|max:2048',
            'cv_attachment' => 'nullable|mimes:pdf|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'signature.mimes' => 'The signature must be a file of type: jpg, png.',
            'signature.max' => 'The signature must not be greater than 2MB',
            'profile_picture.mimes' => 'The profile picture must be a file of type: jpg, png.',
            'profile_picture.max' => 'The profile picture must not be greater than 2MB',
            'cv_attachment.mimes' => 'CV must be a PDF file.',
            'cv_attachment.max' => 'CV must not be larger than 2MB.',
        ];
    }
}
