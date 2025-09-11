<?php

namespace Modules\ConstructionTrack\Requests\ConstructionProgressAttachment;

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
            'attachment'    => 'required|max:2048|mimes:pdf,jpg,jpeg,png',
        ];
    }
}
