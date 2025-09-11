<?php

namespace Modules\Mfr\Requests\Agreement\Amendment;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Mfr\Repositories\AgreementRepository;

class StoreRequest extends FormRequest
{
    public function __construct(protected AgreementRepository $agreements){

}
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
        $startDate = $this->agreements->find($this->agreement)->effective_from;
        return [
            'effective_date'        => 'required',
            'extension_to_date'     => 'required|after:'. $startDate,
            'approved_budget'   => 'required',
            // 'attachment'            => 'required|max:2048|mimes:pdf,jpg,jpeg,png',
        ];
    }

    public function messages()
    {
        return [
            'extension_to_date.after' => 'The extension to date cannot be before the agreement start date.',
        ];
    }
}
