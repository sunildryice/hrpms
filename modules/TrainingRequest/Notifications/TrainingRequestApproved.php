<?php

namespace Modules\TrainingRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\TrainingRequest\Models\TrainingRequest;

class TrainingRequestApproved extends Notification
{
    use Queueable;

    private $trainingRequest;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        TrainingRequest $trainingRequest
    ) {
        $this->trainingRequest = $trainingRequest;
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
        $url = route('training.requests.view', $this->trainingRequest->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line(' Your Training request ' . $this->trainingRequest->getTrainingRequestNumber() . ' has been approved.')
            ->action('View Training Request ', $url)
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
            'training_request_id' => $this->trainingRequest->id,
            'link' => route('training.requests.view', $this->trainingRequest->id),
            'subject' => 'Your Training request ' . $this->trainingRequest->getTrainingRequestNumber() . ' has been approved.'
        ];
    }
}
