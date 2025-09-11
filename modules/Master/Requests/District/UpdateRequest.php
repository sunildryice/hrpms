<?php

namespace Modules\Master\Requests\District;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'province_id'=>['required', 'exists:lkup_provinces,id'],
            'district_name'=>['required']
        ];
    }

    public function messages()
    {
        return [
            'district_name.required'=>'District is required.',
        ];
    }
}
