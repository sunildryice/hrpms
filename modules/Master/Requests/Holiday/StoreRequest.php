<?php

namespace Modules\Master\Requests\Holiday;

use Illuminate\Foundation\Http\FormRequest;

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
            'title'         => 'required',
            'description'   => 'nullable',
            'holiday_date'  => 'required|date',
            'office_ids'    => 'array',
            'only_female'   => 'nullable'
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Department name is required.'
        ];
    }

    /**
     * Get the validated data from the request.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function validated($key = null, $default = null)
    {
        $validated = $this->validator->validated();
        if (isset($validated['only_female'])) {
            $validated['only_female'] = 1;
        } else {
            $validated['only_female'] = 0;
        }
        return data_get($validated, $key, $default);
    }
}
