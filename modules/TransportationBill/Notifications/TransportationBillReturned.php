<?php

namespace Modules\TransportationBill\Notifications;

use App\Events\NotificationPushed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\TransportationBill\Models\TransportationBill;

class TransportationBillReturned extends Notification
{
    use Queueable;

    private $transportationRequest;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        TransportationBill $transportationRequest
    ) {
        $this->transportationRequest = $transportationRequest;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'));
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
            'transportation_bill_id' => $this->transportationRequest->id,
            'link' => route('transportation.bills.edit', $this->transportationRequest->id),
            'subject' => 'Transportation bill ' . $this->transportationRequest->getTransportationBillNumber() . ' has been returned.'
        ];
    }
}
