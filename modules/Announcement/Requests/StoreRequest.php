<?php

namespace Modules\Announcement\Requests;

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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title'             => 'required',
            'description'       => 'required',
            'published_date'    => 'required',
            'expiry_date'       => 'required',
            'attachment'        => 'nullable|max:2048|mimes:pdf,jpg,jpeg,png',
        ];
    }

    public function attributes()
    {
        return [
            'title'             => 'Title',
            'description'       => 'Description',
            'published_date'    => 'Published Date',
            'expiry_date'       => 'Expiry Date',
            'attachment'        => 'Attachment'
        ];
    }
}
