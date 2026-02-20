<?php

namespace Modules\PurchaseRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\PurchaseRequest\Models\PurchaseRequest;

class PurchaseRequestProcurementApproved extends Notification
{
    use Queueable;

    private $purchaseRequest;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        PurchaseRequest $purchaseRequest
    ) {
        $this->purchaseRequest = $purchaseRequest;
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
        $url = route('review.purchase.requests.create', $this->purchaseRequest->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('test')
            ->action('View Purchase Request ', $url)
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
            'purchase_request_id' => $this->purchaseRequest->id,
            'link' => route('approved.purchase.requests.show', $this->purchaseRequest->id),
            'subject' => 'Purchase request ' . $this->purchaseRequest->getPurchaseRequestNumber() . ' has been approved. Please take action',
        ];
    }
}
