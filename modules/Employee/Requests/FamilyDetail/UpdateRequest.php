<?php

namespace Modules\Employee\Requests\FamilyDetail;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Get the URL to redirect to on a validation error.
     *
     * @return string
     */
    protected function getRedirectUrl()
    {
        return route('employees.edit', [$this->employee, 'tab'=>'family-details']);
    }

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
            'family_relation_id'=>'required|exists:lkup_family_relations,id',
            'full_name'=>'required',
            'date_of_birth'=>'nullable|date',
            'province_id'=>'nullable|exists:lkup_provinces,id',
            'district_id'=>'nullable|exists:lkup_districts,id',
            'local_level_id'=>'nullable|exists:lkup_local_levels,id',
            'ward'=>'nullable|numeric',
            'tole'=>'nullable',
            'remarks'=>'nullable',
            'contact_number'=>'nullable|max:17',
        ];
    }
}
