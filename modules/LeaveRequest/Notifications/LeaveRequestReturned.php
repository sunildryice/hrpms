<?php

namespace Modules\LeaveRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\LeaveRequest\Models\LeaveRequest;

class LeaveRequestReturned extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        protected LeaveRequest $leaveRequest
    ) {}

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
    public function toMail($notifiable)
    {
        $url = route('leave.requests.edit', $this->leaveRequest->id);
        return (new MailMessage)
            ->greeting('Dear ' . $this->leaveRequest->getRequesterName() . ',')
            ->line('Your leave request (' . $this->leaveRequest->getLeaveType() . ') has been returned for revision.')
            ->line('Leave Number: ' . $this->leaveRequest->getLeaveNumber())
            ->line('Leave dates: ' . ($this->leaveRequest->start_date ? $this->leaveRequest->start_date->format('d M Y') : '') . ' to ' . ($this->leaveRequest->end_date ? $this->leaveRequest->end_date->format('d M Y') : ''))
            ->line('Returned by: ' . (auth()->user()->full_name ?? ''))
            ->action('Revise leave request', $url);
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
            'leave_request_id' => $this->leaveRequest->id,
            'link' => route('leave.requests.edit', $this->leaveRequest->id),
            'alternate_link' => route('leave.requests.detail', $this->leaveRequest->id),
            'subject' => 'Leave request ' . $this->leaveRequest->getLeaveNumber() . ' has been returned.',
        ];
    }
}
