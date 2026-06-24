<?php

namespace Modules\Project\Requests\ProjectActivityDetail;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'key_accomplishment' => 'required|string',
            'challenge' => 'required|string',
            'lesson_learned' => 'required|string',
        ];
    }
}
