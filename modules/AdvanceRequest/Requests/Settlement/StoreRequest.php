<?php

namespace Modules\AdvanceRequest\Requests\Settlement;

use Illuminate\Foundation\Http\FormRequest;
use Modules\AdvanceRequest\Models\AdvanceRequest;
use Modules\AdvanceRequest\Repositories\AdvanceRequestRepository;

class StoreRequest extends FormRequest
{
    public function __construct(
        AdvanceRequestRepository $advanceRequests
    )
    {
        $this->advanceRequests = $advanceRequests;
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
            'completion_date'=>'required|date',
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
            $advanceRequest = $this->advanceRequests->find($this->advance);
//            if ($this->completion_date < $advanceRequest->request_date || $this->completion_date < $advanceRequest->required_date) {
//                $validator->errors()->add('completion_date', 'Completion date cannot be before advance\'s request date or required date.');
//            }
            if ($this->completion_date < $advanceRequest->request_date) {
                $validator->errors()->add('completion_date', 'Completion date cannot be before advance\'s request date.');
            }
        });
    }
}
