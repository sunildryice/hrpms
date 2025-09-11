<?php

namespace Modules\Mfr\Requests\Agreement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Mfr\Repositories\AgreementRepository;

class UpdateRequest extends FormRequest
{
    public function __construct(protected AgreementRepository $agreements)
    {

    }

    /**
     * Determine if the user is authorized to make this request.
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
        $flag = $this->agreements->find($this->agreement)->latestAmendment()->exists();

        return [
            'partner_organization_id' => 'required',
            'district_id' => 'required',
            'project_id' => 'required',
            'grant_number' => 'required|string',
            'effective_from' => 'required|date',
            'effective_to' => [Rule::excludeIf(function () use ($flag) {
                return $flag;
            }), 'required', 'date'],
            'approved_budget' => [Rule::excludeIf(function () use ($flag) {
                return $flag;
            }), 'required', 'numeric'],
        ];
    }
}
