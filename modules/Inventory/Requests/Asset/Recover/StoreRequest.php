<?php

namespace Modules\Inventory\Requests\Asset\Recover;

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
            'room_numebr' => 'nullable',
            'assigned_office_id' => 'required|exists:lkup_offices,id'
        ];
    }

    public function messages()
    {
        return [];
    }
}
