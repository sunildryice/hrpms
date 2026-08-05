<?php

namespace Modules\Tracker\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventActionPoint extends Model
{
    use HasFactory, ModelEventLogger;

    protected $table = 'event_action_points';

    protected $fillable = [
        'event_id',
        'action_point',
        'created_by',
        'updated_by',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
