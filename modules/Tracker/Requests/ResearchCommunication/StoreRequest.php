<?php

namespace Modules\Tracker\Requests\ResearchCommunication;

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

    protected function prepareForValidation()
    {
        if (!$this->has('herdi_members_involved')) {
            $this->merge(['herdi_members_involved' => []]);
        }

        if ($this->has('platforms')) {
            $platforms = $this->input('platforms');
            foreach ($platforms as $i => $p) {
                $platforms[$i]['views'] = $p['views'] ?? 0;
                $platforms[$i]['link_clicks'] = $p['link_clicks'] ?? 0;
                $platforms[$i]['reactions'] = $p['reactions'] ?? 0;
                $platforms[$i]['shares'] = $p['shares'] ?? 0;
                $platforms[$i]['comments'] = $p['comments'] ?? 0;
            }
            $this->merge(['platforms' => $platforms]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'type_of_publication'       => 'required|in:Journal,Other',
            'herdi_members_involved'    => 'nullable|array',
            'herdi_members_involved.*'  => ['nullable', Rule::exists('employees', 'id')],
            'journal_paper_name'        => 'nullable|string|max:255',
            'publication_url'           => 'nullable|string|max:255',
            'project_id'                => 'nullable|exists:projects,id',
            'platforms'                 => 'nullable|array',
            'platforms.*.platform'      => 'required_with:platforms|in:Bluesky,Facebook,LinkedIn,Twitter,Website,X,Youtube',
            'platforms.*.views'         => 'nullable|integer|min:0',
            'platforms.*.link_clicks'   => 'nullable|integer|min:0',
            'platforms.*.reactions'     => 'nullable|integer|min:0',
            'platforms.*.shares'        => 'nullable|integer|min:0',
            'platforms.*.comments'      => 'nullable|integer|min:0',
        ];

        if ($this->input('type_of_publication') === 'Journal') {
            $rules['publication_title']   = 'required|string|max:255';
            $rules['date_of_publication'] = 'required|date';
            $rules['type_of_post']        = 'nullable|string|max:255';
            $rules['date_posted']         = 'nullable|date';
            $rules['post_title']          = 'nullable|string|max:255';
        } elseif ($this->input('type_of_publication') === 'Other') {
            $rules['publication_title']   = 'nullable|string|max:255';
            $rules['date_of_publication'] = 'nullable|date';
            $rules['type_of_post']        = 'required|string|max:255';
            $rules['date_posted']         = 'required|date';
            $rules['post_title']          = 'required|string|max:255';
            $rules['platforms']           = 'required|array|min:1';
        } else {
            $rules['publication_title']   = 'nullable|string|max:255';
            $rules['date_of_publication'] = 'nullable|date';
            $rules['type_of_post']        = 'nullable|string|max:255';
            $rules['date_posted']         = 'nullable|date';
            $rules['post_title']          = 'nullable|string|max:255';
        }

        return $rules;
    }

    public function attributes()
    {
        return [
            'type_of_publication'       => 'Type of Publication',
            'publication_title'         => 'Publication Title',
            'date_of_publication'       => 'Date of Publication',
            'herdi_members_involved'    => 'HERDI Members Involved',
            'journal_paper_name'        => 'Journal/Paper Name',
            'publication_url'           => 'Publication URL',
            'type_of_post'              => 'Type of Post',
            'date_posted'               => 'Date Posted',
            'post_title'                => 'Post Title',
            'project_id'                => 'Project',
            'platforms'                 => 'Platforms',
            'platforms.*.platform'      => 'Platform',
            'platforms.*.views'         => 'Views',
            'platforms.*.link_clicks'   => 'Link Clicks',
            'platforms.*.reactions'     => 'Reactions',
            'platforms.*.shares'        => 'Shares',
            'platforms.*.comments'      => 'Comments',
        ];
    }
}
