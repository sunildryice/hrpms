<?php

namespace Modules\Employee\Requests\Training;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Get the URL to redirect to on a validation error.
     *
     * @return string
     */
    protected function getRedirectUrl()
    {
        return route('employees.edit', [$this->employee, 'tab'=>'training-details']);
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
            'institution'=>'required',
            'training_topic'=>'required',
            'period_from'=>'required|date',
            'period_to'=>'required|date|after_or_equal:period_from',
            'remarks'=>'nullable',
            'attachment'=>'mimes:png,jpg,pdf|max:2048',
        ];
    }
}
