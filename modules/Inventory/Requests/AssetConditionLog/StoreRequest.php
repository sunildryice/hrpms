<?php

namespace Modules\Inventory\Requests\AssetConditionLog;

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
            'asset_id' => 'required|exists:assets,id',
            'condition_id' => 'required|exists:lkup_conditions,id',
            'description' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute is required.'
        ];
    }

    public function attributes()
    {
        return [
            'asset_id' => 'Asset',
            'condition_id' => 'Condition',
            'description' => 'Description',
        ];
    }
}
