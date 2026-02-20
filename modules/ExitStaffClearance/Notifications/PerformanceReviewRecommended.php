<?php

namespace Modules\ExitStaffClearance\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\ExitStaffClearance\Models\StaffClearance;

class PerformanceReviewRecommended extends Notification
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
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = route('performance.approve.create', $this->staffClearance->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Performance review (' . $this->staffClearance->getReviewType() . ') of ' . $this->staffClearance->employee->getFullName() . ' from ' . $this->staffClearance->getReviewFromDate() . ' to ' . $this->staffClearance->getReviewToDate() . ' has been recommended for approval.')
            ->action('View Performance Review', $url)
        ;
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
        event(new NotificationPushed());
        return [
            'staff_clearance_id' => $this->staffClearance->id,
            'link'                  => route('performance.approve.create', $this->staffClearance->id),
            'subject'               => 'Performance review (' . $this->staffClearance->getReviewType() . ') of ' . $this->staffClearance->employee->getFullName() . ' from ' . $this->staffClearance->getReviewFromDate() . ' to ' . $this->staffClearance->getReviewToDate() . ' has been recommended for approval.'
        ];
    }
}
