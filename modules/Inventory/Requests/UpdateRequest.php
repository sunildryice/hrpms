<?php

namespace Modules\Inventory\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

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
        $canEditPrice = Gate::allows('updatePrice', $this->inventory); // INFO: $this->inventory is loaded through 'Route Model Binding'

        return [
            //     'received_date'=>'required|date',
            //     'discount_amount'=>'nullable|numeric|min:0',
            //     'received_note'=>'nullable',
            //     'invoice_number'=>'nullable|integer',
            //     'btn'=>'required',
            'specification' => 'nullable',
            'voucher_number' => 'nullable',
            'unit_price' => [Rule::requiredIf($canEditPrice) ,Rule::excludeIf(! $canEditPrice)],
        ];
    }

    public function messages()
    {
        return [
            'approver_id.required_if' => 'The approver is required when submitted',
        ];
    }
}
