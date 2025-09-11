<?php

namespace Modules\PurchaseOrder\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\PurchaseOrder\Models\PurchaseOrder;

class PurchaseOrderSubmitted extends Notification
{
    use Queueable;

    private $purchaseOrder;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        PurchaseOrder $purchaseOrder
    )
    {
        $this->purchaseOrder = $purchaseOrder;
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
        $url = route('review.purchase.orders.create', $this->purchaseOrder->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Purchase order '.$this->purchaseOrder->getPurchaseOrderNumber().' has been submitted for your review.')
            ->action('View Purchase Order ', $url)
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
            'purchase_order_id' => $this->purchaseOrder->id,
            'link'=>route('review.purchase.orders.create', $this->purchaseOrder->id),
            'subject'=> 'Purchase order '.$this->purchaseOrder->getPurchaseOrderNumber().' has been submitted. Requester : '.$this->purchaseOrder->getCreatedBy(),
        ];
    }

}
