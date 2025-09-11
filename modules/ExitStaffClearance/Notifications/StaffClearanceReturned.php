<?php

namespace Modules\ExitStaffClearance\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\ExitStaffClearance\Models\StaffClearance;

class StaffClearanceReturned extends Notification
{
    use Queueable;

    private $staffClearance;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        StaffClearance $staffClearance,
    ) {
        $this->staffClearance = $staffClearance;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'staff_clearance_id' => $this->staffClearance->id,
            'link' => route('staff.clearance.edit', $this->staffClearance->id),
            'alternate_link' => route('staff.clearance.index', $this->staffClearance->id),
            'subject' => 'Exit Staff Clearance for '.$this->staffClearance->employee->getFullName().' has been returned.',
        ];
    }
}
