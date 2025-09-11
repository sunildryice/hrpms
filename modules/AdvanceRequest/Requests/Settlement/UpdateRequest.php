<?php

namespace Modules\AdvanceRequest\Requests\Settlement;

use Illuminate\Foundation\Http\FormRequest;
use Modules\AdvanceRequest\Repositories\AdvanceRequestRepository;
use Modules\AdvanceRequest\Repositories\SettlementRepository;

class UpdateRequest extends FormRequest
{
    private $advanceRequests;
    private $settlements;
    public function __construct(
        AdvanceRequestRepository $advanceRequests,
        SettlementRepository $settlements
    )
    {
        $this->advanceRequests = $advanceRequests;
        $this->settlements = $settlements;
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
        return [
            'completion_date'                   => 'required',
            'reason_for_over_or_under_spending' => 'nullable',
            'remarks'                           => 'nullable',
            'reviewer_id'                       => 'required_if:btn,submit',
            'approver_id'                       => 'required_if:btn,submit',
            'btn'                               => 'required',
        ];
    }

    public function messages()
    {
        return [
            'reviewer_id.required_if'   =>  "The reviewer is required when submitted"
        ];
    }

    public function attributes()
    {
        return [
            'completion_date'                   => 'Completion date',
            'reason_for_over_or_under_spending' => 'Reason for over or under spending',
            'remarks'                           => 'Remarks',
            'reviewer_id'                       => 'Reviewer',
            'approver_id'                       => 'Approver'
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // $advanceSettlement = $this->settlements->find($this->settlement);
            // $advanceRequest = $this->advanceRequests->find($advanceSettlement->advance_request_id);

            // if ($this->completion_date < $advanceRequest->request_date || $this->completion_date < $advanceRequest->required_date) {
            //     $validator->errors()->add('completion_date', 'Completion date cannot be before advance\'s request date or required date.');
            // }
        });
    }
}
