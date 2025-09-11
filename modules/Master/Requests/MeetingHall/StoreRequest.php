<?php

namespace Modules\Master\Requests\MeetingHall;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
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
            'office_id'=>'required',
            'title' => ['required',
                Rule::unique('lkup_meeting_halls')
                    ->where('office_id', $this->office_id)
                    ->where('title', $this->title)
            ],
        ];
    }

    public function messages()
    {
        return [
            'title.required'=>'Meeting Hall name is required.',
            'title.unique'=>'Meeting Hall name is already taken.',
        ];
    }
}
