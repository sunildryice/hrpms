<?php

namespace Modules\Inventory\Requests\AssetAssignmentLog;

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
            'assign_asset_id' => 'required|exists:assets,id',
            'assigned_user_id' => 'required|exists:users,id',
            'assigned_on' => 'required|date',
            'status' => 'required',
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
            'assign_asset_id' => 'Assigned asset',
            'assigned_user_id' => 'Assigned user',
            'assigned_on' => 'Assigned date',
            'status' => 'Asset status'
        ];
    }
}
