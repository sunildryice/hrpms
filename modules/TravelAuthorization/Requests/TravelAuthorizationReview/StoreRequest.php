<?php

namespace Modules\TravelAuthorization\Requests\TravelAuthorizationReview;

use Illuminate\Foundation\Http\FormRequest;
use Modules\TravelAuthorization\Repositories\TravelAuthorizationRepository;

class StoreRequest extends FormRequest
{

    public function __construct(protected TravelAuthorizationRepository $travelAuthorization)
    {
    }
    /**
     * Determine if the user is authorized to make this request.
     *
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
            'status_id'=>'required',
            'recommended_to'=>'required_if:status_id,4',
            'log_remarks'=>'required',
        ];
    }

    public function messages()
    {
        return [
            'log_remarks.required'=>'Remarks is required.'
        ];
    }

    // public function validated($key = null, $default = null)
    // {
    //     $validated = $this->validator->validated();

    //     $travel = $this->travelAuthorization->find($this->travel);

    //     if ($validated['status_id'] == config('constant.RECOMMENDED_STATUS')) {
    //         $validated['recommender_id'] = $travel->approver_id;
    //         $validated['approver_id'] = $validated['recommended_to'];
    //     }
    //     return data_get($validated, $key, $default);
    // }
}
