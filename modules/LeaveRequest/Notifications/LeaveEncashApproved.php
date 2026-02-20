<?php

namespace Modules\LeaveRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\LeaveRequest\Models\LeaveEncash;

class LeaveEncashApproved extends Notification
{
    use Queueable;

    private $leaveEncash;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        LeaveEncash $leaveEncash
    ) {
        $this->leaveEncash = $leaveEncash;
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
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
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
            'leave_encash_id' => $this->leaveEncash->id,
            'link' => route('leave.encash.show', $this->leaveEncash->id),
            'subject' => 'Leave encash request ' . $this->leaveEncash->getEncashNumber() . ' has been approved',
        ];
    }
}
