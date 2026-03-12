<?php

namespace Modules\GoodRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\GoodRequest\Models\GoodRequest;

class GoodRequestApproved extends Notification
{
    use Queueable;

    private $goodRequest;
    private $otherUser;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        GoodRequest $goodRequest,
        $otherUser = null
    ) {
        $this->goodRequest = $goodRequest;
        $this->otherUser = $otherUser;
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
        $url = route('assign.good.requests.create', $this->goodRequest->id);
        // $otherName = $this->otherUser?->full_name;
        // $greeting = 'Dear ' . $notifiable->full_name;
        // if ($otherName) {
        //     $greeting .= ' / ' . $otherName;
        // }
        $itemName = $this->goodRequest->latestGoodRequestItem?->item_name
            ?? $this->goodRequest->goodRequestItems->first()?->item_name
            ?? '';
        $requesterName = $this->goodRequest->requester?->getFullName() ?? '';

        return (new MailMessage)
            // ->greeting($greeting)
            ->greeting('Dear ' . ($notifiable->getFullName() ?? $notifiable->full_name) . ',')
            ->line('A goods request for ' . $itemName . ' submitted by ' . $requesterName . ' has been approved and assigned to you for processing. Please review the request and take the necessary action.')
            ->action('View Good Request', $url);
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
        $itemName = $this->goodRequest->latestGoodRequestItem?->item_name
            ?? $this->goodRequest->goodRequestItems->first()?->item_name
            ?? '';
        $requesterName = $this->goodRequest->requester?->getFullName() ?? '';

        return [
            'good_request_id' => $this->goodRequest->id,
            'link' => route('assign.good.requests.create', $this->goodRequest->id),
            'subject' => 'A goods request for ' . $itemName . ' submitted by ' . $requesterName . ' has been approved and assigned to you for processing.'
        ];
    }
}
