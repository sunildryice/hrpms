<?php

namespace Modules\AssetDisposition\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\AssetDisposition\Models\DispositionRequest;

class AssetDispositionRejected extends Notification
{
    use Queueable;

    private $dispositionRequest;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        DispositionRequest $dispositionRequest
    )
    {
        $this->assetDisposition= $dispositionRequest;
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
        $url = route('asset.disposition.show', $this->assetDisposition->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Your Asset Disposition Request has been rejected.')
            ->action('View asset disposition ', $url)
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
            'event_completion_id' => $this->assetDisposition->id,
            'link'=>route('asset.disposition.show', $this->assetDisposition->id),
            'subject'=> 'Asset Disposition Request has been rejected.',
        ];
        }

}
