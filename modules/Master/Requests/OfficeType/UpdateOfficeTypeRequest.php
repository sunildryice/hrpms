<?php

namespace Modules\Master\Requests\OfficeType;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOfficeTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'required'
        ];
    }

    /**
     * Get the attributes that apply to the request.
     * @return array<string, mixed>
     */
    public function attributes()
    {
        return [
            'title' => 'Office Type'
        ];
    }

    public function validated($key = null, $default = null) 
    {
        $validated = $this->validator->validated();
        if (isset($validated['title'])) {
            $validated['title'] = strtolower($_REQUEST['title']);
        }
        return data_get($validated, $key, $default);
    }
}
