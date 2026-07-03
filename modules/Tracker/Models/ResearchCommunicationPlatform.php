<?php

namespace Modules\Tracker\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Privilege\Models\User;

class ResearchCommunicationPlatform extends Model
{
    protected $table = 'research_communication_platforms';

    protected $fillable = [
        'research_communication_id',
        'platform',
        'views',
        'link_clicks',
        'reactions',
        'shares',
        'comments',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'views'       => 'integer',
        'link_clicks' => 'integer',
        'reactions'   => 'integer',
        'shares'      => 'integer',
        'comments'    => 'integer',
    ];

    public function researchCommunication()
    {
        return $this->belongsTo(ResearchCommunication::class, 'research_communication_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->withDefault();
    }
}
