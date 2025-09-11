<?php

namespace Modules\GoodRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\GoodRequest\Models\GoodRequestAsset;

class HandoverApproved extends Notification
{
    use Queueable;

    private $goodRequestAsset;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        GoodRequestAsset $goodRequestAsset
    )
    {
        $this->goodRequestAsset = $goodRequestAsset;
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
        $url = route('assets.index');
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Asset Handover of '.$this->goodRequestAsset->getAssetNumber().' has been approved.')
            ->action('View Asset Handover ', $url)
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
            'good_request_asset_id' => $this->goodRequestAsset->id,
            'link'=>route('assets.index'),
            'subject'=> 'Asset Handover of '.$this->goodRequestAsset->getAssetNumber().' has been approved.'
        ];
    }

}
