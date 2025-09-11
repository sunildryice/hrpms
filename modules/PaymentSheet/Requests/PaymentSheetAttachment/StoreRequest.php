<?php

namespace Modules\PaymentSheet\Requests\PaymentSheetAttachment;

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
            'title'         => 'required',
            'attachment'    => 'nullable|max:2048|mimes:pdf,jpg,jpeg,png',
            'attachment_link' => 'nullable|url',
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
            'attachment'    => 'Attachment',
            'attachment_link' => 'Link'
        ];
    }

    public function validated($key = null, $default = null)
    {
        $validated = $this->validator->validated();
        $validated['link'] = $validated['attachment_link'];
        return data_get($validated,  $key, $default);
    }
}
