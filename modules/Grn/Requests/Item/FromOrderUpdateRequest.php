<?php

namespace Modules\Grn\Requests\Item;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Grn\Models\GrnItem;

class FromOrderUpdateRequest extends FormRequest
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
        $grnItem = GrnItem::find($this->item);
        $maxQuantity = $grnItem->grnitemable->quantity - $grnItem->grnitemable->grnItems->sum('quantity') + $grnItem->quantity;
        return [
            'quantity'=>'required|numeric|min:1|max:'.$maxQuantity,
            'discount_amount'=>'nullable|numeric|min:0',
            'unit_price'=>'required|numeric|min:0',
            'vat_applicable'=>'nullable',
        ];
    }
}
