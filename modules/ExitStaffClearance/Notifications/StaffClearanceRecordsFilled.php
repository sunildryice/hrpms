<?php

namespace Modules\ExitStaffClearance\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\ExitStaffClearance\Models\StaffClearance;

class StaffClearanceRecordsFilled extends Notification
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
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    // public function toMail($notifiable)
    // {
    //     $url = route('performance.fill', $this->staffClearance->id);
    //     return (new MailMessage)
    //         ->greeting('Hello!')
    //         ->line('Your performance review ('.$this->staffClearance->getReviewType().') from '.$this->staffClearance->getReviewFromDate().' to '.$this->staffClearance->getReviewToDate().' has been created.')
    //         ->action('View Performance Review', $url)
    //         ->line('Thank you for using our application!');
    // }

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
        // event(new NotificationPushed());
        return [
            'staff_clearance_id' => $this->staffClearance->id,
            'link' => route('staff.clearance.edit', $this->staffClearance->id),
            'alternate_link' => route('staff.clearance.index', $this->staffClearance->id),
            'subject' => 'Clearance Details for '.$this->staffClearance->employee->getFullName()."'s Exit Staff Clearance has been filled. Please Verify",
        ];
    }
}
