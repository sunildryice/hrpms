<?php

namespace Modules\Tracker\Models;

use Illuminate\Database\Eloquent\Model;

class HrEventRecruitment extends Model
{
    protected $table = 'hr_event_recruitments';

    protected $fillable = [
        'hr_event_id',
        'member_name',
        'gender',
        'onboard_date',
        'position',
    ];

    protected $casts = [
        'onboard_date' => 'date',
    ];

    public function hrEvent()
    {
        return $this->belongsTo(HrEvent::class, 'hr_event_id');
    }
}
