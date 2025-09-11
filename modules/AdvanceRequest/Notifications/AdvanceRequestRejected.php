<?php

namespace Modules\AdvanceRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\AdvanceRequest\Models\AdvanceRequest;

class AdvanceRequestRejected extends Notification
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
    )
    {
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
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
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
            'advance_request_id' => $this->advanceRequest->id,
            'link'=>route('advance.requests.show', $this->advanceRequest->id),
            'subject'=> 'Advance request '.$this->advanceRequest->getAdvanceRequestNumber().' has been rejected.'
        ];
    }

}
