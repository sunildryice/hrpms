<?php

namespace Modules\AdvanceRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\AdvanceRequest\Models\AdvanceRequest;

class AdvanceRequestSubmitted extends Notification
{
    use Queueable;

    private $advanceRequest;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        AdvanceRequest $advanceRequest
    ) {
        $this->advanceRequest = $advanceRequest;
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
        $url = route('verify.advance.requests.create', $this->advanceRequest->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Advance request ' . $this->advanceRequest->getAdvanceRequestNumber() . ' has been submitted by ' . $this->advanceRequest->getRequesterName() . ' for your verification.')
            ->action('View Advance Request ', $url)
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
            'advance_request_id' => $this->advanceRequest->id,
            'link' => route('verify.advance.requests.create', $this->advanceRequest->id),
            'subject' => 'Advance request ' . $this->advanceRequest->getAdvanceRequestNumber() . ' has been submitted by ' . $this->advanceRequest->getRequesterName() . ' for your verification.'
        ];
    }
}
