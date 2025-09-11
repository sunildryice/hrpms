<?php

namespace Modules\LeaveRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\LeaveRequest\Models\LeaveRequest;

class LeaveRequestSubmittedReview extends Notification
{
    use Queueable;

    private $leaveRequest;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        LeaveRequest $leaveRequest
    )
    {
        $this->leaveRequest = $leaveRequest;
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
        $url = route('review.leave.requests.create', $this->leaveRequest->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Leave request '.$this->leaveRequest->getLeaveNumber().' has been submitted for your reviewal.')
            ->action('View leave request ', $url)
            ->line('Thank you for using our application!');
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
            'leave_request_id' => $this->leaveRequest->id,
            'link'=>route('review.leave.requests.create', $this->leaveRequest->id),
            'alternate_link'=>route('leave.requests.detail', $this->leaveRequest->id),
            'subject'=> 'Leave request '.$this->leaveRequest->getLeaveNumber().' has been submitted for you reviewal. Requester : '.$this->leaveRequest->getRequesterName(),
            
        ];
    }

}
