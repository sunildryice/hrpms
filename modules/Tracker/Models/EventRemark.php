<?php

namespace Modules\Tracker\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventRemark extends Model
{
    use HasFactory, ModelEventLogger;

    protected $table = 'event_remarks';

    protected $fillable = [
        'event_id',
        'remark',
        'created_by',
        'updated_by',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
