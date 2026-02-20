<?php

namespace Modules\TravelAuthorization\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\TravelAuthorization\Models\TravelAuthorization;

class TravelAuthorizationSubmitted extends Notification
{
    use Queueable;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        protected TravelAuthorization $travelAuthorization
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
        $url = route('approve.travel.requests.create', $this->travelAuthorization->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Travel request ' . $this->travelAuthorization->getTravelAuthorizationNumber() . ' has been submitted for your approval.')
            ->action('View travel request ', $url)
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
        $action = 'submitted';
        if ($this->travelAuthorization->status_id == config('constant.RECOMMENDED_STATUS')) {
            $action = 'recommended';
        }
        // event(new NotificationPushed());
        $travelAtuhNumber = $this->travelAuthorization->getTravelAuthorizationNumber();
        $requesterName = $this->travelAuthorization->getRequesterName();

        return [
            'travel_authorization_id' => $this->travelAuthorization->id,
            'link' => route('approve.ta.requests.create', $this->travelAuthorization->id),
            'alternate_link' => route('ta.requests.view', $this->travelAuthorization->id),
            'subject' => 'Travel authorization request ' . $travelAtuhNumber . ' has been ' . $action . '. Requester : ' . $requesterName . '.',
        ];
    }
}
