<?php

namespace Modules\Master\Requests\Office;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
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
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function rules(Request $request)
    {
        return [
            'parent_id' => 'nullable|sometimes|exists:lkup_offices,id',
            'district_id' => 'required|exists:lkup_districts,id',
            'office_name' => 'required',
            'office_code' => [
                'required',
                Rule::unique('lkup_offices')->ignore($this->office),
            ],
            'office_type_id'=>'required',
            'phone_number' => 'required',
            'fax_number' => 'nullable',
            'email_address' => [
                'nullable', 'email',
                Rule::unique('lkup_offices')->ignore($this->office),
            ],
            'account_number' => 'nullable',
            'bank_name' => 'nullable',
            'branch_name' => 'nullable',
            'weekend_type'=>'integer|between:1,2',
        ];
    }

    public function validated($key = null, $default = null) 
    {
        $validated = $this->validator->validated();
        if (!isset($validated['parent_id'])) {
            $validated['parent_id'] = 0;
        }
        return data_get($validated, $key, $default);
    }
}
