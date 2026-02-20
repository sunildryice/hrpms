<?php

namespace Modules\EventCompletion\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\EventCompletion\Models\EventCompletion;

class EventCompletionSubmitted extends Notification
{
    use Queueable;

    private $eventCompletion;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        EventCompletion $eventCompletion
    ) {
        $this->eventCompletion = $eventCompletion;
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
        $url = route('approve.event.completion.create', $this->eventCompletion->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Event Completion Report has been submitted by' . $this->eventCompletion->getRequesterName() . 'for your approval.')
            ->action('View event completion ', $url)
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
            'event_completion_id' => $this->eventCompletion->id,
            'link' => route('approve.event.completion.create', $this->eventCompletion->id),
            'alternate_link' => route('event.completion.show', $this->eventCompletion->id),
            'subject' => 'Event Completion Report by ' . $this->eventCompletion->getRequesterName() . ' has been submitted.',
        ];
    }
}
