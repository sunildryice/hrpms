<?php

namespace Modules\DistributionRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\DistributionRequest\Models\DistributionHandover;

class DistributionHandoverSent extends Notification
{
    use Queueable;

    private $distributionHandover;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        DistributionHandover $distributionHandover
    ) {
        $this->distributionHandover = $distributionHandover;
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
        $url = route('distribution.requests.handovers.show', $this->distributionHandover->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Distribution handover ' . $this->distributionHandover->getDistributionHandoverNumber() . ' has been approved.')
            ->action('View Distribution Handover', $url)
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
            'distribution_handover_id'  => $this->distributionHandover->id,
            'link'                      => route('receive.distribution.requests.handovers.edit', $this->distributionHandover->id),
            'subject'                   => 'Distribution handover ' . $this->distributionHandover->getDistributionHandoverNumber() . ' has been sent.'
        ];
    }
}
