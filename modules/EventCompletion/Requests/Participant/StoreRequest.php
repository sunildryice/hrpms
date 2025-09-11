<?php

namespace Modules\EventCompletion\Requests\Participant;

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
            'name'=>'required',
            'office'=>'required',
            'designation'=>'nullable',
            'contact'=>'nullable',
        ];
    }

    public function messages()
    {
        return [];
    }
}
