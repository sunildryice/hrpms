<?php

namespace Modules\TravelRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\TravelRequest\Models\TravelClaim;

class TravelClaimReturned extends Notification
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
    )
    {
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
            'travel_claim_id' => $this->travelClaim->id,
            'link'=>route('travel.claims.edit', $this->travelClaim->id),
            'alternate_link'=>route('travel.claims.view', $this->travelClaim->id),
            'subject'=> 'Travel claim for travel request '.$this->travelClaim->travelRequest->getTravelRequestNumber().' has been returned.'
        ];
    }

}
