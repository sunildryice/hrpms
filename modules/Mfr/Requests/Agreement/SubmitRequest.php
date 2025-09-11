<?php

namespace Modules\Mfr\Requests\Agreement;

use Illuminate\Foundation\Http\FormRequest;

class SubmitRequest extends FormRequest
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
            'remarks' => 'nullable',
            'reviewer_id' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'reviewer_id'   => 'reviewer'
        ];
    }
}
