<?php

namespace Modules\MeetingHallBooking\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\MeetingHallBooking\Models\MeetingHallBooking;
use Modules\Privilege\Models\User;

class MeetingHallBookingPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the given meeting hall booking request can be cancelled by the user.
     *
     * @param  \Modules\TravelRequest\Models\TravelRequest  $travelRequest
     * @return bool
     */
    public function cancel(User $user, MeetingHallBooking $meetingHallBooking)
    {
        return in_array($meetingHallBooking->status_id, [3]) && in_array($user->id, [$meetingHallBooking->created_by])
            && $meetingHallBooking->meeting_date >= date('Y-m-d');
    }

    public function reverseCancel(User $user, MeetingHallBooking $meetingHallBooking)
    {
        return in_array($meetingHallBooking->status_id, [config('constant.CANCELLED_STATUS')]) && in_array($user->id, [$meetingHallBooking->created_by])
            && $meetingHallBooking->meeting_date >= date('Y-m-d');
    }

    /**
     * Determine if the given travel request can be deleted by the user.
     *
     * @param  \Modules\TravelRequest\Models\TravelRequest  $travelRequest
     * @return bool
     */
    public function delete(User $user, MeetingHallBooking $meetingHallBooking)
    {
        return in_array($meetingHallBooking->status_id, [1, 2]) && in_array($user->id, [$meetingHallBooking->created_by]);
    }

    /**
     * Determine if the given travel request can be submitted by the user.
     *
     * @param  \Modules\TravelRequest\Models\TravelRequest  $travelRequest
     * @param  \Modules\TravelRequest\Models\TravelRequestItinerary  $travelRequestItinerary
     * @return bool
     */
    public function submit(User $user, MeetingHallBooking $meetingHallBooking)
    {
        return in_array($meetingHallBooking->status_id, [1, 2]) && in_array($user->id, [$meetingHallBooking->created_by]);
    }

    /**
     * Determine if the given travel request can be updated by the user.
     *
     * @param  \Modules\TravelRequest\Models\TravelRequest  $travelRequest
     * @return bool
     */
    public function update(User $user, MeetingHallBooking $meetingHallBooking)
    {
        return in_array($meetingHallBooking->status_id, [1, 2]) && in_array($user->id, [$meetingHallBooking->created_by]);
    }
}
