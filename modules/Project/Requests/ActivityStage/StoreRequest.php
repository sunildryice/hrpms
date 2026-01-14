<?php

namespace Modules\Project\Requests\ActivityStage;


use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'activated' => 'nullable',
        ];
    }
}
