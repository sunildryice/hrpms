<?php

namespace Modules\TravelRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\TravelRequest\Models\TravelClaim;

class TravelClaimSubmitted extends Notification
{
    use Queueable;

    private $travelClaim;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        TravelClaim $travelClaim
    ) {
        $this->travelClaim = $travelClaim;
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
        $url = route('review.travel.claims.create', $this->travelClaim->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Travel claim ' . $this->travelClaim->travelRequest->getTravelRequestNumber() . ' has been submitted for your review.')
            ->action('View travel claim ', $url)
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
            'travel_claim_id' => $this->travelClaim->id,
            'link' => route('review.travel.claims.create', $this->travelClaim->id),
            'alternate_link' => route('travel.claims.view', $this->travelClaim->id),
            'subject' => 'Travel claim for travel request ' . $this->travelClaim->travelRequest->getTravelRequestNumber() . ' has been submitted. Requester : ' . $this->travelClaim->travelRequest->getRequesterName() . '.',
        ];
    }
}
