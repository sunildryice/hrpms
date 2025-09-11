<?php

namespace Modules\MeetingHallBooking\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;

use Modules\Master\Models\MeetingHall;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class MeetingHallBooking extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'meeting_hall_bookings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'meeting_hall_id',
        'meeting_date',
        'start_time',
        'end_time',
        'purpose',
        'number_of_attendees',
        'remarks',
        'status_id',
        'created_by',
        'updated_by',
    ];

    protected $dates = ['meeting_date'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the approver
     */
    public function bookedBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    /**
     * Get the office of the employee.
     */
    public function meetingHall()
    {
        return $this->belongsTo(MeetingHall::class, 'meeting_hall_id');
    }

     /**
     * Get the booking status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    public function getBookedBy()
    {
        return $this->bookedBy->full_name;
    }

    public function getEndTime()
    {
        return \Carbon\Carbon::createFromFormat('H:i:s', $this->end_time)->format('h:i A');
    }

    public function getMeetingHall()
    {
        return $this->meetingHall->title;
    }

    public function getStartTime()
    {
        return \Carbon\Carbon::createFromFormat('H:i:s', $this->start_time)->format('h:i A');
    }

    public function getStatus()
    {
        if($this->status->id == 3){
            return 'Booked';
        }
        return $this->status->title;
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }
}
