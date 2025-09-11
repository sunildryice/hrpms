<?php

namespace Modules\PurchaseRequest\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\PurchaseRequest\Models\PurchaseRequest;

class PurchaseRequestRecommended extends Notification
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
    )
    {
        $this->purchaseRequest = $purchaseRequest;

        if($purchaseRequest->status_id == config('constant.RECOMMENDED_STATUS')){
            $this->message = 'Purchase request '.$this->purchaseRequest->getPurchaseRequestNumber().' has been recommended for your review. Requester : '.$this->purchaseRequest->getRequesterName();
            $this->url = route('review.recommended.purchase.requests.create', $this->purchaseRequest->id);
        }
        if($purchaseRequest->status_id == config('constant.RECOMMENDED2_STATUS')){
            $this->message = 'Purchase request '.$this->purchaseRequest->getPurchaseRequestNumber().' has been recommended for your approval. Requester : '.$this->purchaseRequest->getRequesterName();
            $this->url = route('approve.recommended.purchase.requests.create',$this->purchaseRequest->id);
        }
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
        $url = route('approve.purchase.requests.create', $this->purchaseRequest->id);
        return (new MailMessage)
            ->greeting('Hello!')
            ->line('Purchase request '.$this->purchaseRequest->getPurchaseRequestNumber().' has been submitted for your approve.')
            ->action('View Purchase Request ', $url)
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
            'purchase_request_id' => $this->purchaseRequest->id,
            'link'=> $this->url,
            'subject'=> $this->message
        ];
    }

}
