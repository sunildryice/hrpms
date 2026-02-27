<?php

namespace Modules\LieuLeave\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

use Illuminate\Notifications\Notification;
use Modules\LieuLeave\Models\LieuLeaveRequest;

class LieuLeaveRequestSubmitted extends Notification
{
    use Queueable;

    protected $lieuLeaveRequest;

    public function __construct(
        LieuLeaveRequest $lieuLeaveRequest
    ) {
        $this->lieuLeaveRequest = $lieuLeaveRequest;
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
        $url = route('approve.lieu.leave.requests.show', $this->lieuLeaveRequest->id);
        return (new MailMessage)
            ->greeting('Hey ' . $this->lieuLeaveRequest->getApproverName() . ',')
            ->line('You have a new lieu leave request awaiting your approval.')
            ->line('Employee: ' . $this->lieuLeaveRequest->getRequesterName())
            ->line('Leave Number: ' . $this->lieuLeaveRequest->getLeaveNumber())
            ->line('Leave dates: ' . ($this->lieuLeaveRequest->start_date ? $this->lieuLeaveRequest->getStartDate() : '') . ' to ' . ($this->lieuLeaveRequest->end_date ? $this->lieuLeaveRequest->getEndDate() : ''))
            ->line('Reason: ' . $this->lieuLeaveRequest->reason)
            ->action('View lieu leave request', $url);
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
            'lieu_leave_id' => $this->lieuLeaveRequest->id,
            'link' => route('lieu.leave.requests.show', $this->lieuLeaveRequest->id),
            'alternate_link' => route('lieu.leave.requests.show', $this->lieuLeaveRequest->id),
            'subject' => 'Lieu leave request ' . $this->lieuLeaveRequest->id . ' has been submitted. Requester : ' . $this->lieuLeaveRequest->requester->full_name,
        ];
    }
}
