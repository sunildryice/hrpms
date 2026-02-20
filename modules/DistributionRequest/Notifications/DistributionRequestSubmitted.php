<?php

namespace Modules\DistributionRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\DistributionRequest\Models\DistributionRequest;

class DistributionRequestSubmitted extends Notification
{
    use Queueable;

    private $distributionRequest;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        DistributionRequest $distributionRequest
    ) {
        $this->distributionRequest = $distributionRequest;
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
        $url = route('approve.distribution.requests.create', $this->distributionRequest->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Distribution request ' . $this->distributionRequest->getDistributionRequestNumber() . ' has been submitted.')
            ->action('View Distribution Request', $url)
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
            'distribution_request_id'   => $this->distributionRequest->id,
            'link'                      => route('approve.distribution.requests.create', $this->distributionRequest->id),
            'subject'                   => 'Distribution request ' . $this->distributionRequest->getDistributionRequestNumber() . ' has been submitted.'
        ];
    }
}
