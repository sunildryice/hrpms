<?php

namespace Modules\GoodRequest\Requests\DirectDispatch;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\GoodRequest\Models\GoodRequest;
use Modules\GoodRequest\Models\GoodRequestItem;
use Modules\Inventory\Models\InventoryItem;

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
            'assigned_quantity.*' => Rule::requiredIf($this->status_id == config('constant.APPROVED_STATUS')),
            'status_id' => 'required',
            'log_remarks' => 'required'
        ];
    }

    // public function attributes()
    // {
    //     return [
    //         'status_id' => 'status',
    //         'log_remarks' => 'remarks'
    //     ];
    // }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $validatedInputs = $validator->validated();
            if (!empty($validatedInputs['assigned_quantity'])) {
                foreach($validatedInputs['assigned_quantity'] as $key => $value) {
                    $goodRequestItem = GoodRequestItem::findOrFail($key);
                    if ($value > $goodRequestItem->assignedInventoryItem->getAvailableQuantity()) {
                        $validator->errors()->add('assigned_quantity', 'The assigned quantity exceeds the available quantity.');
                    }
                }
            }
        });
    }
}
