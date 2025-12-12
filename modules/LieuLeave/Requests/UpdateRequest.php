<?php

namespace Modules\LieuLeave\Requests;


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
            'leave_date'    => 'required|date',
            'reason'        => 'required|string',
            'send_to'       => 'required|exists:users,id',
            'substitutes'   => 'nullable|array',
            'substitutes.*' => 'exists:employees,id',
            'btn'           => 'required|string',
        ];
    }
}
