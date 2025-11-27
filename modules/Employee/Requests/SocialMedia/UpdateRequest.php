<?php

namespace Modules\Employee\Requests\SocialMedia;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

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
        $rules = [];
        $socialMediaAccounts = DB::table('lkup_social_accounts')->pluck('title');

        foreach ($socialMediaAccounts as $account) {
            $rules[strtolower($account)] = 'nullable|url|max:255';
        }

        $rules['bio'] = 'nullable|string|max:1000';

        return $rules;
    }
}
