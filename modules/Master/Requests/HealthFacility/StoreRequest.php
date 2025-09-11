<?php

namespace Modules\Master\Requests\HealthFacility;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'title' => 'required|unique:lkup_health_facilities',
            'province_id' => 'required|exists:lkup_provinces,id',
            'district_id' => 'required|exists:lkup_districts,id',
            'local_level_id' => 'required|exists:lkup_local_levels,id',
            'ward' => 'required|numeric',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.required'=>'Health facility is required.',
            'title.unique'=>'Health facility is already taken.',
            'local_level_id.required'=>'Palika is required.',
        ];
    }
}
