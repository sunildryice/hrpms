<?php

namespace Modules\TravelAuthorization\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\TravelAuthorization\Models\TravelAuthorization;

class TravelAuthorizationApproved extends Notification
{
    use Queueable;

    private $travel;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        TravelAuthorization $travel
    )
    {
        $this->travelAuthorization = $travel;
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
            'travel_request_id' => $this->travelAuthorization->id,
            'link'=>route('ta.requests.view', $this->travelAuthorization->id),
            'alternate_link'=>route('ta.requests.view', $this->travelAuthorization->id),
            'subject'=> 'Travel Authorization request '.$this->travelAuthorization->getTravelAuthorizationNumber().' has been approved.'
        ];
    }

}
