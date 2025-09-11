<?php

namespace Modules\ConstructionTrack\Requests\ConstructionInstallment;

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
            'advance_release_date'  => 'required',
            'transaction_type_id'   => 'required',
            'remarks'               => 'required',
            'amount'                => 'required|numeric|min:0.01'
        ];
    }
}
