<?php

namespace Modules\TravelRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\TravelRequest\Models\LocalTravel;

class LocalTravelReturned extends Notification
{
    use Queueable;

    private $localTravel;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        LocalTravel $localTravel
    ) {
        $this->localTravel = $localTravel;
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
            'local_travel_reimbursement_id' => $this->localTravel->id,
            'link' => route('local.travel.reimbursements.edit', $this->localTravel->id),
            'alternate_link' => route('local.travel.reimbursements.show', $this->localTravel->id),
            'subject' => 'Local travel reimbursement ' . $this->localTravel->getLocalTravelNumber() . ' has been returned.'
        ];
    }
}
