<?php

namespace Modules\ConstructionTrack\Requests\ConstructionProgress;

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
            'report_date'=>'required',
            // 'work_start_date'=>'required',
            // 'work_completion_date'=>'required',
            'progress_percentage'=>'required|numeric|min:0.01',
            'estimate'=>'nullable|string',
            'remarks'=>'required',
            'btn'=>'required',
        ];
    }
}
