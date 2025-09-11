<?php

namespace Modules\ConstructionTrack\Requests\ConstructionAttachment;

use Illuminate\Foundation\Http\FormRequest;

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
            'title'         => 'required',
            'attachment'    => 'nullable|max:2048|mimes:pdf,jpg,jpeg,png',
            'link' => 'nullable|url'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'title'         => 'Title',
            'Attachment'    => 'Attachment'
        ];
    }
}
