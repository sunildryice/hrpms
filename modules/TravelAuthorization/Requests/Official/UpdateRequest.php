<?php

namespace Modules\TravelAuthorization\Requests\Official;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
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
            'name' =>   'required|string',
            'post' =>   'nullable|string',
            'level' =>  'nullable|string',
            'office' => 'nullable|string',
            'district_id' => 'required|exists:lkup_districts,id',
        ];
    }

    public function messages()
    {
        return [];
    }
}
