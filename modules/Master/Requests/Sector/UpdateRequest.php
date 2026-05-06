<?php

namespace Modules\Master\Requests\Sector;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user();
    }

    public function rules()
    {
        return [
            'title' => 'required',
        ];
    }
}
