<?php

namespace Modules\ConstructionTrack\Requests\ConstructionAmendment;

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
            'effective_date'        => 'required',
            'extension_to_date'     => 'nullable',
            'total_estimate_cost'   => 'required',
            // 'attachment'            => 'required|max:2048|mimes:pdf,jpg,jpeg,png',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'effective_date'        => 'Effective date',
            'total_estimate_cost'   => 'Total Estimate Cost',
            // 'Attachment'            => 'Attachment'
        ];
    }
}
