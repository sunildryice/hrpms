<?php

namespace Modules\Project\Requests\OtherDetail;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'key' => 'required|string',
            'value' => 'required|string',
        ];
    }
}
