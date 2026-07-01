<?php

namespace Modules\Tracker\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Privilege\Models\User;
use Modules\Project\Models\Project;

class ResearchCommunication extends Model
{
    use HasFactory, ModelEventLogger;

    protected $table = 'research_communication';

    protected $fillable = [
        'project_id',
        'type_of_publication',
        'publication_title',
        'date_of_publication',
        'herdi_members_involved',
        'journal_paper_name',
        'publication_url',
        'type_of_post',
        'date_posted',
        'post_title',
        'posted_in',
        'views',
        'link_clicks',
        'reactions',
        'shares',
        'comments',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date_of_publication' => 'date',
        'date_posted'         => 'date',
        'views'               => 'integer',
        'link_clicks'         => 'integer',
        'reactions'           => 'integer',
        'shares'              => 'integer',
        'comments'            => 'integer',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function getPublicationTypeLabel()
    {
        return match ($this->type_of_publication) {
            'Journal' => 'Research Article',
            'Other'   => 'Other Posts',
            default   => $this->type_of_publication ?? 'N/A',
        };
    }

    public function getProjectTitle()
    {
        return $this->project?->short_name ?? $this->project?->title ?? 'N/A';
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->withDefault();
    }

    public function getDateOfPublication()
    {
        return $this->date_of_publication?->toFormattedDateString();
    }

    public function getDatePosted()
    {
        return $this->date_posted?->toFormattedDateString();
    }

    public function getCreatorName()
    {
        return $this->createdBy->getFullName();
    }

    public function getTotalEngagement()
    {
        return $this->views + $this->link_clicks + $this->reactions + $this->shares + $this->comments;
    }
}
