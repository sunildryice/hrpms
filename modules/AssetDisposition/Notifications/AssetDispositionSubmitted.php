<?php

namespace Modules\AssetDisposition\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\AssetDisposition\Models\DispositionRequest;

class AssetDispositionSubmitted extends Notification
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
        $url = route('approve.asset.disposition.create', $this->assetDisposition->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Asset Disposition Request has been submitted by'. $this->assetDisposition->getRequesterName() .'for your approval.')
            ->action('View Asset Disposition Request ', $url)
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
            'link'=>route('approve.asset.disposition.create', $this->assetDisposition->id),
            'alternate_link'=>route('asset.disposition.show', $this->assetDisposition->id),
            'subject'=> 'Asset Disposition Request by '.$this->assetDisposition->getRequesterName().' has been submitted for your approval.',
        ];
    }

}
