<?php

namespace Modules\Master\Requests\LocalLevel;

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
            'district_id'=>['required', 'exists:lkup_districts,id'],
            'local_level_name'=>['required']
        ];
    }

    public function messages()
    {
        return [
            'district_name.required'=>'District is required.',
        ];
    }
}
