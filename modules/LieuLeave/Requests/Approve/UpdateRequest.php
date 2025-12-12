<?php

namespace Modules\LieuLeave\Requests\Approve;

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
            'status_id' => 'required|exists:lkup_status,id',
            'approver_remarks' => 'nullable|string',
        ];
    }
}
