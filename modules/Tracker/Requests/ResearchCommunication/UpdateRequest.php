<?php

namespace Modules\Tracker\Requests\ResearchCommunication;

use Illuminate\Foundation\Http\FormRequest;

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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'type_of_publication'       => 'nullable|in:Journal,Other',
            'publication_title'         => 'nullable|string|max:255',
            'date_of_publication'       => 'nullable|date',
            'herdi_members_involved'    => 'nullable|string|max:255',
            'journal_paper_name'        => 'nullable|string|max:255',
            'publication_url'           => 'nullable|string|max:255',
            'type_of_post'              => 'nullable|string|max:255',
            'date_posted'               => 'nullable|date',
            'post_title'                => 'nullable|string|max:255',
            'project_id'                => 'nullable|exists:projects,id',
            'posted_in'                 => 'nullable|in:Bluesky,Facebook,LinkedIn,Twitter,Website,X,Youtube',
            'views'                     => 'nullable|integer|min:0',
            'link_clicks'               => 'nullable|integer|min:0',
            'reactions'                 => 'nullable|integer|min:0',
            'shares'                    => 'nullable|integer|min:0',
            'comments'                  => 'nullable|integer|min:0',
        ];
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
            'posted_in'                 => 'Posted In',
            'views'                     => 'Views',
            'link_clicks'               => 'Link Clicks',
            'reactions'                 => 'Reactions',
            'shares'                    => 'Shares',
            'comments'                  => 'Comments',
        ];
    }
}
