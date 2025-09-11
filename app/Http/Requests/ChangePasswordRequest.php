<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
            'current_password' => 'required|old_password:' . auth()->user()->password,
            'new_password' => 'required|different:current_password',
            'confirm_password' => 'required|same:new_password'
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'current_password.required' => 'Current password is required.',
            'current_password.old_password' => 'Current password did not match.',
            'new_password.required' => 'New password is required.',
            'new_password.different' => 'New password must not matched with current password.',
            'confirm_password.required' => 'Confirm password is required.',
            'confirm_password.same' => 'Confirm password must be matched with new password.'
        ];
    }
}
