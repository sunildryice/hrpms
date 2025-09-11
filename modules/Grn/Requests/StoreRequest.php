<?php

namespace Modules\Grn\Requests;

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
            'received_date'=>'required|date',
            'received_note'=>'nullable',
            'invoice_number'=>'nullable|string',
            'supplier_id'=>'nullable|exists:suppliers,id',
        ];
    }
}
