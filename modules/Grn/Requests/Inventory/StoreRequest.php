<?php

namespace Modules\Grn\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Grn\Models\GrnItem;

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
            'distribution_type_id'=>'required|exists:lkup_distribution_types,id',
            'execution_id'=>'nullable|exists:lkup_executions,id',
            'expiry_date'=>'nullable|date',
            'specification'=>'nullable',
            'purchase_date'=>'nullable',
            'interpreter_ids'=>'nullable|array'
        ];
    }
}
