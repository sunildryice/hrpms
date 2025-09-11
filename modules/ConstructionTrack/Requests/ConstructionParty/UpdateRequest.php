<?php

namespace Modules\ConstructionTrack\Requests\ConstructionParty;

use Illuminate\Foundation\Http\FormRequest;

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
            'party_name'=>'required',
            'contribution_amount'=>'required|numeric|min:0.01',
            // 'contribution_percentage'=>'required|numeric|min:0.01'
        ];
    }
}
