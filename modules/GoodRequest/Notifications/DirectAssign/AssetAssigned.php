<?php

namespace Modules\GoodRequest\Notifications\DirectAssign;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\GoodRequest\Models\GoodRequest;

class AssetAssigned extends Notification
{
    use Queueable;

    private $goodRequest;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        GoodRequest $goodRequest
    ) {
        $this->goodRequest = $goodRequest;
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
        $url = route('good.requests.direct.dispatch.approve.create', $this->goodRequest->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Direct dispatch good request ' . $this->goodRequest->getGoodRequestNumber() . ' has been submitted for your approval/assignment.')
            ->action('View Direct Dispatch Good Request', $url)
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
        $url = route('receive.good.requests.direct.assign.create', $this->goodRequest->id);
        return [
            'good_request_id' => $this->goodRequest->id,
            'link' => $url,
            'subject' => 'Asset ' . $this->goodRequest->goodRequestAssets()->first()->getAssetNumber() . ' has been assigned to you through ' . $this->goodRequest->getGoodRequestNumber() . '. Please take action.'
        ];
    }
}
