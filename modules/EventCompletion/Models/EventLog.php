<?php

namespace Modules\EventCompletion\Models;

use App\Traits\ModelEventLogger;
use Modules\Privilege\Models\User;

use Illuminate\Database\Eloquent\Model;
use Modules\EventCompletion\Models\EventCompletion;
use Illuminate\Database\Eloquent\Factories\HasFactory;



class EventLog extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'event_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_completion_id',
        'user_id',
        'original_user_id',
        'log_remarks',
        'status_id',
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

    /**
     * Get the createdBy of the log.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    /**
     * Get the status of the travel request log.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    public function getCreatedAt()
    {
        return $this->created_at->format('M d, Y h:i A');

    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    } 

    public function getDesignation()
    {
        return $this->createdBy->employee->tenures()->where('created_at', '<=', $this->created_at)->latest()->first()->getDesignationName() ?? '';
    }

}
