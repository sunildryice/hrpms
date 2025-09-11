<?php

namespace Modules\PurchaseRequest\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'required_date'=>'required|date',
            'purpose'=>'nullable',
            'delivery_instructions'=>'nullable',
            'procurement_officer' => 'nullable',
            'attachment' => 'nullable|max:5120|mimes:pdf,jpg,jpeg,png,xls,xlsx',
        ];
    }

    public function attributes()
    {
        return [
            'attachment' => 'Attachment',
        ];
    }
}
