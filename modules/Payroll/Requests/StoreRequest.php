<?php

namespace Modules\Payroll\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
    public function rules(Request $request)
    {
        return [
            'fiscal_year_id' => 'required',
            'month' => [
                'required',
                Rule::unique('payroll_batches')->where(function($query) use ($request){
                    return $query->where('fiscal_year_id', $request->fiscal_year_id);
                })
            ],
            'posted_date' => 'nullable',
            'description' => 'nullable',
        ];
    }
}
