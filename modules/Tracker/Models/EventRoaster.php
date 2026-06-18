<?php

namespace Modules\Tracker\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventRoaster extends Model
{
    use HasFactory, ModelEventLogger;

    protected $table = 'event_roasters';

    protected $fillable = [
        'event_id',
        'organisation',
        'organisation_name',
        'position',
        'ethnicity',
        'gender',
        'created_by',
        'updated_by',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
