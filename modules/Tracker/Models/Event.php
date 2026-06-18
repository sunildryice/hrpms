<?php

namespace Modules\Tracker\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Project\Models\Project;

class Event extends Model
{
    use HasFactory, ModelEventLogger;

    protected $table = 'events';

    protected $fillable = [
        'project_id',
        'event_organized_by',
        'event_type',
        'event_name',
        'from_date',
        'to_date',
        'country',
        'province',
        'district',
        'city_local_level',
        'organized_by',
        'role',
        'total_participants_government',
        'total_herdi_participants',
        'total_other_participants',
        'roaster_details',
        'action_points',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'from_date'                     => 'date',
        'to_date'                       => 'date',
        'total_participants_government' => 'integer',
        'total_herdi_participants'      => 'integer',
        'total_other_participants'      => 'integer',
        'roaster_details'               => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function roasters()
    {
        return $this->hasMany(EventRoaster::class);
    }

    public function getFromDate()
    {
        return $this->from_date?->toFormattedDateString();
    }

    public function getToDate()
    {
        return $this->to_date?->toFormattedDateString();
    }

    public function getProjectTitle()
    {
        return $this->project?->title ?? 'N/A';
    }

    public function getTotalParticipants()
    {
        return ($this->total_participants_government ?? 0)
             + ($this->total_herdi_participants ?? 0)
             + ($this->total_other_participants ?? 0);
    }
}
