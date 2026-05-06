<?php

namespace Modules\Master\Requests\Sector;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user();
    }

    public function rules()
    {
        return [
            'title' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Sector name is required.',
        ];
    }
}
