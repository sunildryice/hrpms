<?php

namespace Modules\Master\Requests\LocalLevel;

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
            'district_id'=>['required', 'exists:lkup_districts,id'],
            'local_level_name'=>'required',
        ];
    }

    public function messages()
    {
        return [
            'province_name.required'=>'Province name is required.',
        ];
    }
}
