<?php

namespace Modules\OffDayWork\Requests\Approve;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'status_id' => 'required|in:' . implode(',', [
                config('constant.APPROVED_STATUS'),
                config('constant.REJECTED_STATUS'),
            ]),
            'approver_remarks' => 'nullable|string|max:255',
        ];
    }
}
