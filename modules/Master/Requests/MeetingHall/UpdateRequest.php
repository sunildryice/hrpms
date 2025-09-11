<?php

namespace Modules\Master\Requests\MeetingHall;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'title' => ['required',
                //ue('lkup_meeting_halls')
                    // ->where('office_id', $this->office_id)
                    // ->where('title', $this->title)
                    // ->ignore($this->title)
            ],
            'office_id'=>'required'
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
