<?php

namespace Modules\Master\Requests\DonorCode;

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
            // 'title'=>[
            //     'required',
            //     Rule::unique('lkup_donor_codes')->ignore($this->donorCode),
            // ],
            'description'=>'nullable'
        ];
    }

    public function messages()
    {
        return [
            'title.required'=>'Donor code is required.',
            'title.unique'=>'Donor code is already taken.',
        ];
    }
}
