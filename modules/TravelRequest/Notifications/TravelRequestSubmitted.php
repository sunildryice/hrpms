<?php

namespace Modules\TravelRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\TravelRequest\Models\TravelRequest;

class TravelRequestSubmitted extends Notification
{
    use Queueable;

    private $travelRequest;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        TravelRequest $travelRequest
    ) {
        $this->travelRequest = $travelRequest;
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
        $url = route('approve.travel.requests.create', $this->travelRequest->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Travel request ' . $this->travelRequest->getTravelRequestNumber() . ' has been submitted for your approval.')
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
        event(new NotificationPushed());
        return [
            'travel_request_id' => $this->travelRequest->id,
            'link' => route('approve.travel.requests.create', $this->travelRequest->id),
            'alternate_link' => route('travel.requests.view', $this->travelRequest->id),
            'subject' => 'Travel request ' . $this->travelRequest->getTravelRequestNumber() . ' has been submitted. Requester : ' . $this->travelRequest->getRequesterName() . '.',
        ];
    }
}
