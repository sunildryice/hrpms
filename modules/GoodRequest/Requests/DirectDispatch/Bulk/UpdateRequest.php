<?php

namespace Modules\GoodRequest\Requests\DirectDispatch\Bulk;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\GoodRequest\Models\GoodRequest;
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
            'assigned_quantity' => Rule::requiredIf($this->status_id == config('constant.APPROVED_STATUS')),
            'status_id' => 'required',
            'log_remarks' => 'required'
        ];
    }

    public function attributes()
    {
        return [
            'assigned_quantity' => 'assigned quantity',
            'status_id' => 'status',
            'log_remarks' => 'remarks'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $validatedInputs = $validator->validated();
            if (!empty($validatedInputs['assigned_quantity'])) {
                $goodRequest = GoodRequest::findOrFail($this->goodRequest);
                $goodRequestItem = $goodRequest->latestGoodRequestItem;
                $inventoryItem = InventoryItem::findOrFail($goodRequestItem->assigned_inventory_item_id);
                if ($validatedInputs['assigned_quantity'] > $inventoryItem->getAvailableQuantity()) {
                    $validator->errors()->add('assigned_quantity', 'The assigned quantity exceeds the available quantity.');
                }
            }
        });
    }
}
