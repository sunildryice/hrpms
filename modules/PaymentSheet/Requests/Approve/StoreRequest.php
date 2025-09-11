<?php

namespace Modules\PaymentSheet\Requests\Approve;

use Illuminate\Foundation\Http\FormRequest;
use Modules\PaymentSheet\Repositories\PaymentSheetRepository;

class StoreRequest extends FormRequest
{
    private $paymentSheets;

    public function __construct(
        PaymentSheetRepository $paymentSheets
    )
    {
        $this->paymentSheets = $paymentSheets;
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
            'status_id'=>'required',
            'reviewer_id'=>'exclude_unless:status_id,'.config('constant.RECOMMENDED_STATUS'),
            'approver_id'=>'required_if:status_id,'.config('constant.RECOMMENDED_STATUS').'|exclude_unless:status_id,'.config('constant.RECOMMENDED_STATUS'),
            'log_remarks'=>'required',
        ];
    }

    public function messages()
    {
        return [
            'log_remarks.required'=>'Remarks is required.'
        ];
    }

    public function validated($key = null, $default = null)
    {
        $validated = $this->validator->validated();

        $paymentSheet = $this->paymentSheets->find($this->paymentSheet);

        if ($validated['status_id'] == config('constant.RECOMMENDED_STATUS')) {
            $validated['recommender_id'] = $paymentSheet->approver_id;
        }

        if (!isset($validated['reviewer_id']) && $validated['status_id'] == config('constant.RECOMMENDED_STATUS')) {
            $validated['status_id'] = config('constant.RECOMMENDED2_STATUS');
        }

        return data_get($validated, $key, $default);
    }
}
