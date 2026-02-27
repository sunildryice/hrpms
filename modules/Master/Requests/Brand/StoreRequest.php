<?php

namespace Modules\Master\Requests\Brand;

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
            'title'       => 'required|unique:lkup_brands,title',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Brand name is required.',
            'title.unique'   => 'This brand name is already taken.',
        ];
    }
}