<?php

namespace Modules\AssetDisposition\Requests\Participant;

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
            'name'=>'required',
            'office'=>'required',
            'designation'=>'sometimes|required',
            'contact'=>'sometimes|required',
        ];
    }

    public function messages()
    {
        return [];
    }
}
