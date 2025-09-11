<?php

namespace Modules\EventCompletion\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Model;

use Modules\EventCompletion\Models\EventCompletion;
use Illuminate\Database\Eloquent\Factories\HasFactory;



class EventParticipant extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'event_participants';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_completion_id',
        'name',
        'office',
        'designation',
        'contact'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the approved log for the local travel.
     */
    public function eventCompletion()
    {
        return $this->belongsTo(EventCompletion::class, 'event_completion_id');
    }

    public function getFullName()
    {
        return ucfirst($this->name);
    }

}
