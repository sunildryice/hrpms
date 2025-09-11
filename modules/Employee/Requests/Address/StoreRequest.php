<?php

namespace Modules\Employee\Requests\Address;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Get the URL to redirect to on a validation error.
     *
     * @return string
     */
    // protected function getRedirectUrl()
    // {
    //     return route('employees.edit', [$this->employee, 'tab'=>'address']);
    // }

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
            'temporary_province_id'=>'required',
            'temporary_district_id'=>'required',
            'temporary_local_level_id'=>'required',
            'temporary_ward'=>'required|numeric|min:1|max:33',
            'temporary_tole'=>'required',
            'permanent_province_id'=>'required_unless:validationcurrent,checked',
            'permanent_district_id'=>'required_unless:validationcurrent,checked',
            'permanent_local_level_id'=>'required_unless:validationcurrent,checked',
            'permanent_ward'=>'required_unless:validationcurrent,checked|nullable|numeric|min:1|max:33',
            'permanent_tole'=>'required_unless:validationcurrent,checked',
            'current_location'=>'nullable|url',
//            'validationcurrent'=>'nullable'
        ];
    }

    public function messages()
    {
        return [
            'permanent_province_id.required_unless'=>'Permanent province is required.',
            'permanent_district_id.required_unless'=>'Permanent province is required.',
            'permanent_local_level_id.required_unless'=>'Permanent province is required.',
            'permanent_ward.required_unless'=>'Permanent province is required.',
            'permanent_tole.required_unless'=>'Permanent province is required.',
        ];
    }
}
