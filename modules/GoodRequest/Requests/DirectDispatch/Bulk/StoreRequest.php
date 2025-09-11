<?php

namespace Modules\GoodRequest\Requests\DirectDispatch\Bulk;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Inventory\Models\InventoryItem;

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
            'employee_ids' => 'required|array',
            'purpose' => 'required',
            'office_id' => 'required',
            'approver_id' => 'required',
            'handover_date' => 'required|date',
            'btn' => 'required',
            'dispatch_item.assigned_inventory_item_id.*' => 'required',
            'dispatch_item.assigned_quantity.*' => 'required',
        ];

    }

    public function attributes()
    {
        return [
            'purpose' => 'Purpose',
            'office_id' => 'Office',
            'approver_id' => 'Approver',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $validatedInputs = $validator->validated();
            foreach ($validatedInputs['dispatch_item']['assigned_inventory_item_id'] as $index => $invId) {
                $inventoryItem = InventoryItem::findOrFail($invId);
                if ($validatedInputs['dispatch_item']['assigned_quantity'][$index] > $inventoryItem->getAvailableQuantity()) {
                    $validator->errors()->add('assigned_quantity', 'The requested quantity exceeds the available quantity.');
                    break;
                }
            }
        });
    }
}
