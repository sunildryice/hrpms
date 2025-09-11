<?php

namespace Modules\GoodRequest\Requests\DirectDispatch;

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
            'purpose' => 'required',
            'quantity' => 'required',
            'office_id' => 'required',
            'receiver_id' => 'required',
            'approver_id' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'purpose' => 'Purpose',
            'quantity' => 'Quantity',
            'office_id' => 'Office',
            'receiver_id' => 'Receiver',
            'approver_id' => 'Approver',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $validatedInputs = $validator->validated();
            $inventoryItem = InventoryItem::findOrFail($this->inventoryItem);
            if ($validatedInputs['quantity'] > $inventoryItem->getAvailableQuantity()) {
                $validator->errors()->add('quantity', 'The requested quantity exceeds the available quantity.');
            }
        });
    }
}
