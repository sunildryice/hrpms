<?php

namespace Modules\WorkFromHome\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\WorkFromHome\Models\WorkFromHome;

class WorkFromHomeRequestApproved extends Notification
{
    use Queueable;

    private $workFromHomeRequest;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        WorkFromHome $workFromHomeRequest
    ) {
        $this->workFromHomeRequest = $workFromHomeRequest;
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
        $url = route('approve.wfh.requests.show', $this->workFromHomeRequest->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Work from home request ' . $this->workFromHomeRequest->getRequestId() . ' has been approved.')
            ->action('View work from home request ', $url)
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
        event(new NotificationPushed());
        return [
            'work_from_home_id' => $this->workFromHomeRequest->id,
            'link' => route('wfh.requests.show', $this->workFromHomeRequest->id),
            'alternate_link' => route('wfh.requests.show', $this->workFromHomeRequest->id),
            'subject' => 'Work from home request ' . $this->workFromHomeRequest->getRequestId() . ' has been approved. Requester : ' . $this->workFromHomeRequest->requester->full_name,
        ];
    }
}
