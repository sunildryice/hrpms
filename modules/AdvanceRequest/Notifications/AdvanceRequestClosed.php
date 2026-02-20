<?php

namespace Modules\AdvanceRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\AdvanceRequest\Models\AdvanceRequest;

class AdvanceRequestClosed extends Notification
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
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'));
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
            'purchase_request_id' => $this->advanceRequest->id,
            'link' => route('advance.requests.show', $this->advanceRequest->id),
            'alternate_link' => route('advance.requests.index'),
            'subject' => 'Advance Request ' . $this->advanceRequest->getAdvanceRequestNumber() . ' has been closed.'
        ];
    }
}
