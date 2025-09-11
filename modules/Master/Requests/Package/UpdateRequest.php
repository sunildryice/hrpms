<?php

namespace Modules\Master\Requests\Package;

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
            'package_name' => 'required',
            'package_description' => 'nullable',
        ];
    }

    public function attributes()
    {
        return [
            'package_name' => 'The package name is required.',
        ];
    }
}
