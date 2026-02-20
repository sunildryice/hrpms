<?php

namespace Modules\AdvanceRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\AdvanceRequest\Models\AdvanceRequest;

class AdvancePaid extends Notification
{
    use Queueable;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        protected AdvanceRequest $advance
    ) {}

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
        $url = route('advance.requests.show', $this->advance->id);

        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Advanced Request  ' . $this->advance->getAdvanceRequestNumber() . 'has been processed and paid.')
            ->action('View advanced settlement ', $url)
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
            'advance_request_id' => $this->advance->id,
            'link' => route('advance.requests.show', $this->advance->id),
            'subject' => 'Advance request: ' . $this->advance->getAdvanceRequestNumber() . " as been marked as paid",
        ];
    }
}
