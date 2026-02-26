<?php

namespace Modules\Inventory\Requests\Asset;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'serial_number' => 'nullable',
            'remarks'       => 'nullable',
            'voucher_number' => 'nullable',
            'room_number' => 'nullable',
            'brand' => 'nullable|exists:lkup_brands,id',
            'model_number' => 'nullable',
        ];
    }

    public function attributes()
    {
        return [
            'serial_number' => 'Serial number',
            'remarks'       => 'Remarks'
        ];
    }
}
